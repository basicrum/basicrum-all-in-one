<?php

declare(strict_types=1);

namespace App\BasicRum;

use PHPUnit\Framework\TestCase;

class DiagramSchemaTest extends TestCase
{
    public function testDistributionSchema()
    {
        $distributionSchema = file_get_contents(__DIR__.'/distributionSchema.json');

        $schema = new DiagramSchema('distribution');
        $data = $schema->generateSchema();

        $this->assertEquals(
            $distributionSchema,
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    public function testTimeSeriesSchema()
    {
        $timeSeriesSchema = file_get_contents(__DIR__.'/timeSeriesSchema.json');

        $schema = new DiagramSchema('time_series');
        $data = $schema->generateSchema();

        $this->assertEquals(
            $timeSeriesSchema,
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }
}
