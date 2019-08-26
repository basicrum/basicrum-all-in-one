<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Runner;

class ComplexSelect
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var \Symfony\Component\Cache\Adapter\FilesystemAdapter */
    private $cacheAdapter;

    public function __construct(
        \Doctrine\Bundle\DoctrineBundle\Registry $registry,
        \Symfony\Component\Cache\Adapter\FilesystemAdapter $cacheAdapter
    )
    {
        $this->registry     = $registry;
        $this->cacheAdapter = $cacheAdapter;
    }

    public function process($complexSelect, array $filters) : array
    {
        $repository = $this->registry
            ->getRepository($this->getEntityClassName($complexSelect->getSecondarySelectEntityName()));

        $queryBuilder = $repository->createQueryBuilder($complexSelect->getSecondarySelectEntityName());

        $selects = [];

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\Select $select */
        foreach ($complexSelect->getSecondarySelectDataFieldNames() as $field) {
            $selects[] = $complexSelect->getSecondarySelectEntityName() . '.' . $field;
        }

        foreach ($filters as $filter) {
            $corrected = str_replace(
                $complexSelect->getPrimarySelectEntityName() . '.' . $complexSelect->getPrimaryKeyFieldName(),
                $complexSelect->getSecondarySelectEntityName()  . '.' . $complexSelect->getSecondaryKeyFieldName(),
                $filter
            );
            $queryBuilder->andWhere($corrected);
        }

        $queryBuilder->select($selects);

        $res = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);

        $data = [];

        // We need $complexSelect->getSecondaryKeyFieldName() as a key in new array because we will use it later for IN clause
        foreach ($res as $row) {
            $data[$row[$complexSelect->getSecondaryKeyFieldName()]] = $row;
        }

        return $data;
    }

    /**
     * @param string $className
     * @return string
     */
    public function getEntityClassName(string $className) : string
    {
        return '\App\Entity\\' . $className;
    }

}