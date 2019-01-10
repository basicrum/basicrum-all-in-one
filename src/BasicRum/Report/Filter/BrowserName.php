<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Filter;

class BrowserName implements FilterInterface
{

    const INTERNAL_IDENTIFIER = 'browser_name';

    /**
     * @return string
     */
    public function getFilterLabel()
    {
        return 'Browser';
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
        $fieldName  = 'ntuser_agent_' . self::INTERNAL_IDENTIFIER . '.' . $this->_transformToEntityProperty(self::INTERNAL_IDENTIFIER);

        $queryBuilder
            ->leftJoin(
                'App\Entity\NavigationTimingsUserAgents',
                'ntuser_agent_' . self::INTERNAL_IDENTIFIER,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                "nt.userAgentId = ntuser_agent_" . self::INTERNAL_IDENTIFIER . ".id"
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