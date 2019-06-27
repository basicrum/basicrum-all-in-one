<?php

namespace App\Tests\BasicRum;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Command\CacheCleanCommand;

class NoFixturesTestCase extends KernelTestCase
{

    protected function setUp()
    {
        $cacheCommand = new CacheCleanCommand();
        $cacheCommand->clearCache();

        static::bootKernel();

        $this->truncateEntities($this->getAllEntityClasses());
    }

    // Idea from https://symfonycasts.com/screencast/phpunit/control-database

    private function getEntityManager()
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @return array
     */
    private function getAllEntityClasses() : array
    {
        $classes = [];

        $metas = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        foreach ($metas as $meta) {
            $classes[] = $meta->getName();
        }

        return $classes;
    }

    /**
     * @param array $entities
     */
    private function truncateEntities(array $entities)
    {
        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        }
        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->getEntityManager()->getClassMetadata($entity)->getTableName()
            );
            $connection->executeUpdate($query);
        }
        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        }
    }

}