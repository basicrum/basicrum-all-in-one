<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Filter;

use App\Entity\PageTypeConfig;

class PageType implements FilterInterface
{

    const INTERNAL_IDENTIFIER = 'page_type';

    /**
     * @return string
     */
    public function getFilterLabel()
    {
        return 'Page Type';
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
        $pageTypeRepo = $queryBuilder->getEntityManager()->getRepository(PageTypeConfig::class);
        $pageType = $pageTypeRepo->findOneBy(['id' => $value]);

        $value = $pageType->getConditionTerm();

        $urlFilter = new Url();

        $urlFilter->attachTo($value, $condition, $queryBuilder);
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