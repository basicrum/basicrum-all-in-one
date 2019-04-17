<?php
declare(strict_types=1);

namespace App\BasicRum\Boomerang;

class Builder
{
    /**
     * Info links:
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.NavigationTiming.html
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.ResourceTiming.html
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.PaintTiming.html
     *  - https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.Mobile.html
     */
    private $plugins = [
        'navigation_timings'  => [
            'label'       => 'Navigation Timings',
            'description' => '',
            'docs_link'   => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.NavigationTiming.html',
            'file_name'   => 'navtiming.js'
        ],
        'resource_timings' => [
            'label'       => 'Resource Timings',
            'description' => '',
            'docs_link'   => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.ResourceTiming.html',
            'file_name'   => 'restiming.js'
        ],
        'paint_timings' => [
            'label'       => 'Paint Timings',
            'description' => '',
            'docs_link'   => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.PaintTiming.html',
            'file_name'   => 'painttiming.js'
        ],
        'network_information' => [
            'label'       => 'Mobile / Network Information',
            'description' => '',
            'docs_link'   => 'https://developer.akamai.com/tools/boomerang/docs/BOOMR.plugins.Mobile.html',
            'file_name'   => 'mobile.js'
        ],
        'google_analytics' => [
            'label'       => 'Google Analytics - Session Capture',
            'description' => '',
            'docs_link'   => '',
            'file_name'   => 'google-analytics-customised.js'
        ],
        'guid' => [
            'label'       => 'GUID (Session Cookie)',
            'description' => '',
            'docs_link'   => '',
            'file_name'   => 'guid-customised.js'
        ]
    ];

    private $boomerangFileName = 'boomerang.js';

    private $buildSource = '';

    public function __construct()
    {
        $this->buildSource = '2019-q1';
    }

    /**
     * @return array
     */
    public function getAvailablePlugins() : array
    {
        return $this->plugins;
    }

    /**
     * @param array $buildParams
     * @return bool
     * @throws \Exception
     */
    public function build(array $buildParams)
    {
        $beaconPushUrl = !empty($buildParams['beacon_catcher_address']) ? $buildParams['beacon_catcher_address'] : '';
        $boomerangPlugins = !empty($buildParams['plugins']) ? array_keys($buildParams['plugins']) : [];

        if (empty($beaconPushUrl)) {
            throw new \Exception('Failed - Beacon url not specified!');
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
            $script .= "\n" . $this->readFileContent($pluginFile);
        }

        $script .= "\n" . $initPartTpl;

        $ch = curl_init('https://closure-compiler.appspot.com/compile');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=WHITESPACE_ONLY&js_code=' . rawurlencode($script));
        $build = curl_exec($ch);

        curl_close($ch);

        //@todo: save the build

        return $build;
    }

    /**
     * @param string $fileKey
     * @return string
     * @throws \Exception
     */
    private function readFileContent(string $fileKey) : string
    {
        $path = __DIR__ . '/Js/' . $this->buildSource . '/' . $fileKey;

        if (!file_exists($path)) {
            throw new \Exception("File {$fileKey} doesn't exist!");
        }

        return file_get_contents($path);
    }

}