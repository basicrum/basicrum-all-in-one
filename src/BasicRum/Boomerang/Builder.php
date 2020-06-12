<?php

declare(strict_types=1);

namespace App\BasicRum\Boomerang;

use App\Entity\BoomerangBuilds;

class Builder
{
    /**
     * Info links:
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.NavigationTiming.html
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.ResourceTiming.html
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.PaintTiming.html
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.Mobile.html.
     */
    private $plugins = [
        'rum_data_flat' => [
            'label' => 'Navigation Timings',
            'description' => '',
            'docs_link' => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.NavigationTiming.html',
            'file_name' => 'navtiming.js',
        ],
        'resource_timings' => [
            'label' => 'Resource Timings',
            'description' => '',
            'docs_link' => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.ResourceTiming.html',
            'file_name' => 'restiming.js',
        ],
        'paint_timings' => [
            'label' => 'Paint Timings',
            'description' => '',
            'docs_link' => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.PaintTiming.html',
            'file_name' => 'painttiming.js',
        ],
        'network_information' => [
            'label' => 'Mobile / Network Information',
            'description' => '',
            'docs_link' => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.Mobile.html',
            'file_name' => 'mobile.js',
        ],
        'google_analytics' => [
            'label' => 'Google Analytics - Session Capture',
            'description' => '',
            'docs_link' => '',
            'file_name' => 'google-analytics-customised.js',
        ],
        'session_cookie' => [
            'label' => 'GUID (Session Cookie)',
            'description' => '',
            'docs_link' => '',
            'file_name' => 'guid-customised.js',
        ],
    ];

    private $boomerangFileName = 'boomerang.js';

    private $buildSource = '';

    public function __construct()
    {
        $this->buildSource = '2019-q1';
    }

    public function getAvailablePlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @return array
     */
    public function getAllBuilds(
        \Doctrine\Common\Persistence\ManagerRegistry $doctrine
    ) {
        $builds = $doctrine
            ->getManager()
            ->getRepository(BoomerangBuilds::class)
            ->findAll();

        return array_reverse($builds);
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    public function build(
        array $buildParams,
        \Doctrine\Common\Persistence\ManagerRegistry $doctrine
    ) {
        $beaconPushUrl = !empty($buildParams['beacon_catcher_address']) ? $buildParams['beacon_catcher_address'] : '';
        $boomerangPlugins = !empty($buildParams['plugins']) ? array_keys($buildParams['plugins']) : [];

        if (empty($beaconPushUrl)) {
            throw new \Exception('Failed - Beacon url not specified!');
        }

        if (false === filter_var($beaconPushUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception('Failed - Not valid beacon url!');
        }

        if (empty($boomerangPlugins)) {
            throw new \Exception('Failed - No plugins specified!');
        }

        $initPartTpl = <<<EOT
BOOMR.init({
    beacon_url: "{$beaconPushUrl}"
});
EOT;

        $script = '';

        $script .= $this->readFileContent($this->boomerangFileName);

        foreach ($boomerangPlugins as $plugin) {
            $pluginFile = $this->plugins[$plugin]['file_name'];
            $script .= "\n".$this->readFileContent($pluginFile);
        }

        $script .= "\n".$initPartTpl;

        $ch = curl_init('https://closure-compiler.appspot.com/compile');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=WHITESPACE_ONLY&js_code='.rawurlencode($script));
        $buildResult = curl_exec($ch);

        curl_close($ch);

        if (empty($buildResult)) {
            throw new \Exception('Build compile process failed!');
        }

        $version = $this->getVersion($boomerangPlugins);

        //Add version to boomerang
        $buildResult = str_replace('%boomerang_version%', $version, $buildResult);

        $build = new BoomerangBuilds();

        $build->setBuildResult($buildResult);
        $build->setBuildParams(json_encode($buildParams));
        $build->setBoomerangVersion($version);
        $build->setCreatedAt(new \DateTime('now'));

        $em = $doctrine->getManager();
        $em->persist($build);
        $em->flush();

        return $build->getId();
    }

    /**
     * @throws \Exception
     */
    private function readFileContent(string $fileKey): string
    {
        $path = __DIR__.'/Js/'.$this->buildSource.'/'.$fileKey;

        if (!file_exists($path)) {
            throw new \Exception("File {$fileKey} doesn't exist!");
        }

        return file_get_contents($path);
    }

    /**
     * @return string
     */
    private function getVersion(array $plugins)
    {
        $version = '';

        foreach ($plugins as $plugin) {
            $parts = explode('_', $plugin);
            foreach ($parts as $part) {
                $version .= $part[0];
            }
            $version .= '|';
        }

        $version .= (string) time();

        return $version;
    }

    /**
     * @param $buildId
     */
    public function getBuildInfo(
        $buildId,
        \Doctrine\Common\Persistence\ManagerRegistry $doctrine
    ): array {
        /** @var \App\Entity\BoomerangBuilds $build */
        $build = $doctrine
            ->getManager()
            ->getRepository(BoomerangBuilds::class)
            ->find($buildId);

        $buildPlugins = [];

        $buildParams = json_decode($build->getBuildParams(), true);

        foreach ($buildParams['plugins'] as $plugin => $val) {
            $buildPlugins[$plugin] = $this->plugins[$plugin];
        }

        return [
            'boomerang_version' => $build->getBoomerangVersion(),
            'boomerang_plugins' => $buildPlugins,
            'beacon_catcher_address' => $buildParams['beacon_catcher_address'],
        ];
    }

    /**
     * @param $buildId
     */
    public function getBuild(
        $buildId,
        \Doctrine\Common\Persistence\ManagerRegistry $doctrine
    ): BoomerangBuilds {
        /** @var \App\Entity\BoomerangBuilds $build */
        $build = $doctrine
            ->getManager()
            ->getRepository(BoomerangBuilds::class)
            ->find($buildId);

        return $build;
    }
}
