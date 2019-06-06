<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\ResourceTimings;

class Url
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $_urlsPairs = [];

    /** @var int */
    private $_pairsCount = 0;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;

        $this->_reloadPairs();

        $this->_pairsCount = count($this->_urlsPairs);
    }


    /**
     * Returns pair ['navigation timing key array key' => 'url id']
     *
     * @param array $data
     *
     * @return array
     */
    public function insertUrls(array $data)
    {
        $pairs = [];

        $mustFlush = false;

        foreach ($data as $key => $row) {
            $url = explode('?', $key)[0];

            if (isset($this->_urlsPairs[$url])) {
                $pairs[$key] = $this->_urlsPairs[$url];
            } else {
                $mustFlush = true;

                $this->_pairsCount++;

                $resourceUrl = new \App\Entity\ResourceTimingsUrls();
                $resourceUrl->setUrl($url);
                $resourceUrl->setCreatedAt(new \DateTime());

                $this->registry->getManager()->persist($resourceUrl);

                // Speculatively append to current url pairs
                $this->_urlsPairs[$url] = $this->_pairsCount;
                $pairs[$key] = $this->_pairsCount;
            }
        }

        if ($mustFlush) {
            $this->registry->getManager()->flush();
            $this->registry->getManager()->clear();
        }

        return $pairs;
    }

    private function _reloadPairs()
    {
        $repository = $this->registry
            ->getRepository(\App\Entity\ResourceTimingsUrls::class);

        $queryBuilder = $repository->createQueryBuilder('rtu');

        $queryBuilder->select(['rtu.id', 'rtu.url']);

        $data = $queryBuilder->getQuery()->getArrayResult();

        foreach ($data as $row) {
            $this->_urlsPairs[$row['url']] = $row['id'];
        }
    }

}