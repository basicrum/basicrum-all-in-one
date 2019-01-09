<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Filter;

class DeviceType implements FilterInterface
{

    const INTERNAL_IDENTIFIER = 'device_type';

    /**
     * @return string
     */
    public function getFilterLabel()
    {
        return 'Device';
    }

    /**
     * @param $value
     * @param string $condition
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function attachTo(
        $value,
        string $condition,
        \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $fieldName  = 'ntuser_agent.' . $this->_transformToEntityProperty(self::INTERNAL_IDENTIFIER);

        $queryBuilder
            ->leftJoin(
                'App\Entity\NavigationTimingsUserAgents',
                'ntuser_agent',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                "nt.userAgentId = ntuser_agent.id"
            )
            ->andWhere($fieldName . ' = :' . $this->getInternalIdentifier())
            ->setParameter($this->getInternalIdentifier(), $value);
    }

    /**
     * @return string
     */
    public function getInternalIdentifier()
    {
        return self::INTERNAL_IDENTIFIER;
    }

    /**
     * @param string $var
     * @return string
     */
    private function _transformToEntityProperty(string $var)
    {
        return lcfirst(str_replace(" ", "",ucwords(str_replace("_", " ", $var))));
    }

}