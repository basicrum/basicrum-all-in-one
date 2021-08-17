<?php

namespace  App\Tests\BasicRum\Beacon\Catcher\Storage\File;

use PHPUnit\Framework\TestCase;


class TimeTest extends TestCase
{

    /**
     * @group catcher
     */
    public function testGetTimeFromBeaconFilePath()
    {
        $paths = [
            'www_whatever_com_ea92ec8fd5cfcaf2dad6360fa6c8da1a-1557221203',
            'www_whatever_com_ea92ec8fd5cfcaf2dad6360fa6c8da1a-1557221203-888',
            'path-333-hh/www_whatever_com_ea92ec8fd5cfcaf2dad6360fa6c8da1a-1557221203-888',
            'www_2dashes-rh_com-1554612260'
        ];

        $expectations = [
            1557221203,
            1557221203,
            1557221203,
            1554612260
        ];

        $time = new \App\BasicRum\Beacon\Catcher\Storage\File\Time();

        $actual = [];

        foreach ($paths as $path)
        {
            $actual[] = $time->getCreatedAtFromPath($path);
        }

        //var_dump($result);

        $this->assertEquals(
            $expectations,
            $actual
        );
    }

}