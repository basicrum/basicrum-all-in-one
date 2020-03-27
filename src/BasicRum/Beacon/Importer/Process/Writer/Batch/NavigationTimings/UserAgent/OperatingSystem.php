<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent;

use App\Entity\OperatingSystems;

class OperatingSystem
{
    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $osCodeIdMap = [];

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
        $this->reloadCodeIdMap();
    }

    public function getOsIdByName(string $name): int
    {
        if (empty($name)) {
            $name = 'Unknown';
        }

        $code = $this->getCodeByName($name);

        $id = isset($this->osCodeIdMap[$code]) ?
            $this->osCodeIdMap[$code] : $this->insertOs($name, $code);

        return (int) $id;
    }

    private function insertOs(string $name, string $code): int
    {
        $name = trim($name);

        $os = new OperatingSystems();
        $os->setCode($code);
        $os->setLabel($name);

        $this->registry->getManager()->persist($os);

        $this->registry->getManager()->flush();
        $this->registry->getManager()->clear();

        $id = \count($this->osCodeIdMap) + 1;

        $this->osCodeIdMap[$code] = $id;

        return $id;
    }

    /**
     * @return string
     */
    private function getCodeByName(string $name)
    {
        return strtolower(
            str_replace(
                ' ',
                '_',
                trim(
                    $name
                )
            )
        );
    }

    private function reloadCodeIdMap()
    {
        $repository = $this->registry
            ->getRepository(OperatingSystems::class);

        $queryBuilder = $repository->createQueryBuilder('os');

        $queryBuilder->select(['os.id', 'os.code']);

        $data = $queryBuilder->getQuery()->getArrayResult();

        foreach ($data as $row) {
            $this->osCodeIdMap[$row['code']] = $row['id'];
        }
    }
}
