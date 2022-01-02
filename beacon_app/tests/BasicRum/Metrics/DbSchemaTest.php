<?php

namespace  App\Tests\BasicRum\Metrics;

use PHPUnit\Framework\TestCase;

use App\BasicRum\Metrics\DbSchemaCollaborator;

class DbSchemaTest extends TestCase
{

    /**
     * @group schema
     */
    public function testColumnsSanityCheck()
    {
        $collaborator = new DbSchemaCollaborator();

        $collaborator->getDbColumnsInfo();

        $this->assertEquals(
            [
                "connect_duration" => [
                    'name' => 'connect_duration',
                    'type' => 'Nullable(UInt16)'
                ],
                "first_contentful_paint" => [
                    'name' => 'first_contentful_paint',
                    'type' => 'Nullable(UInt16)'
                ],
                "cumulative_layout_shift" => [
                    'name' => 'cumulative_layout_shift',
                    'type' => 'Nullable(Float32)'
                ],
                "largest_contentful_paint" => [
                    'name' => 'largest_contentful_paint',
                    'type' => 'Nullable(UInt16)'
                ],
                "first_input_delay" => [
                    'name' => 'first_input_delay',
                    'type' => 'Nullable(UInt16)'
                ],
                "first_paint" => [
                    'name' => 'first_paint',
                    'type' => 'Nullable(UInt16)'
                ],
                "load_event_end" => [
                    'name' => 'load_event_end',
                    'type' => 'Nullable(UInt16)'
                ],
                "redirects_count" => [
                    'name' => 'redirects_count',
                    'type' => 'UInt8'
                ],
                "first_byte" => [
                    'name' => 'first_byte',
                    'type' => 'Nullable(UInt16)'
                ],
                "dns_duration" => [
                    'name' => 'dns_duration',
                    'type' => 'Nullable(UInt16)'
                ],
                "redirect_duration" => [
                    'name' => 'redirect_duration',
                    'type' => 'Nullable(UInt16)'
                ],
                "download_time" => [
                    'name' => 'download_time',
                    'type' => 'Nullable(UInt16)'
                ],
                "session_id" => [
                    'name' => 'session_id',
                    'type' => 'FixedString(43)'
                ],
                "session_length" => [
                    'name' => 'session_length',
                    'type' => 'UInt8'
                ],
                "url" => [
                    'name' => 'url',
                    'type' => 'String'
                ],
                "user_agent" => [
                    'name' => 'user_agent',
                    'type' => 'String'
                ],
                "request_type" => [
                    'name' => 'request_type',
                    'type' => 'LowCardinality(String)'
                ],
                "created_at" => [
                    'name' => 'created_at',
                    'type' => 'DateTime'
                ],
                "browser_name" => [
                    'name' => 'browser_name',
                    'type' => 'LowCardinality(String)'
                ],
                "browser_version" => [
                    'name' => 'browser_version',
                    'type' => 'String'
                ],
                "device_type" => [
                    'name' => 'device_type',
                    'type' => 'LowCardinality(String)'
                ],
                "device_manufacturer" => [
                    'name' => 'device_manufacturer',
                    'type' => 'LowCardinality(String)'
                ]

            ],
            $collaborator->getDbColumnsInfo()
        );
    }

}