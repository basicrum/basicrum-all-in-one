<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Filter;

class UserAgent implements FilterInterface
{

    const INTERNAL_IDENTIFIER = 'user_agent';

    /**
     * @return string
     */
    public function getFilterLabel()
    {
        return 'User Agent';
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
        $fieldName  = 'nt.' . $this->_transformToEntityProperty(self::INTERNAL_IDENTIFIER);

        $queryBuilder
            ->andWhere($fieldName . ' LIKE :' . $this->getInternalIdentifier())
            ->setParameter($this->getInternalIdentifier(), '%' . $value . '%');
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