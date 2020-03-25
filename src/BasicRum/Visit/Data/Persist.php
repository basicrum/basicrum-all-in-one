<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Data;

use App\Entity\VisitsOverview;

class Persist
{

    /** @var int */
    private $batchSize = 500;

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry   = $registry;
    }

    /**
     * @param array $visits
     * @return Persist
     */
    public function saveVisits(array $visits) : self
    {
        $cnt = 1;

        foreach ($visits as $visit) {
            $cnt++;

            if ($visit['visitId'] !== false) {
                $entity = $this->registry->getRepository(VisitsOverview::class)
                    ->find($visit['visitId']);
            } else {
                $entity = new VisitsOverview();
            }

            $entity->setGuid($visit['guid']);
            $entity->setpageViewsCount($visit['pageViewsCount']);
            $entity->setFirstPageViewId($visit['firstPageViewId']);
            $entity->setLastPageViewId($visit['lastPageViewId']);
            $entity->setFirstUrlId($visit['firstUrlId']);
            $entity->setLastUrlId($visit['lastUrlId']);
            $entity->setCompleted($visit['completed']);
            $entity->setVisitDuration($visit['visitDuration']);
            $entity->setAfterLastVisitDuration($visit['afterLastVisitDuration']);

            $this->registry->getManager()->persist($entity);

            if (($cnt % $this->batchSize) === 0) {
                $this->registry->getManager()->flush();
                $this->registry->getManager()->clear();
            }
        }

        $this->registry->getManager()->flush();
        $this->registry->getManager()->clear();

        return $this;
    }

}