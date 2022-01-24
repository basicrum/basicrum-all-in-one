<?php

namespace  App\Tests\BasicRum;

use App\BasicRum\EventsStorage\Storage;
use App\BasicRum\Db\ClickHouse\Connection;
use App\BasicRum\Workflows\ImportBundles;

use PHPUnit\Framework\TestCase;

class ImportBundlesTest extends TestCase
{

    public function testSanity()
    {
        $clickHouseConnectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storageMock = $this->getMockBuilder(Storage::class)
            ->setMethodsExcept(['getBundleBeacons'])
            ->disableOriginalConstructor()
            ->getMock();

        $storageMock
            ->expects($this->exactly(1))
            ->method('listBundlesInHosts')
            ->willReturn([
                'testhost1_com' => [
                    '/testhost1_com/1.json',
                    '/testhost1_com/2.json'
                ],
                'testhost2_com' => [
                    '/testhost2_com/3.json',
                    '/testhost2_com/4.json'
                ],
                'badbundles_com' => [
                    '/badbundles_com/1.json'
                ],
                'complete_corrupted_bundle_com' => [
                    '/complete_corrupted_bundle_com/1.json'
                ],
            ]);

        $storageMock
            ->expects($this->exactly(6))
            ->method('readBundle')
            ->withConsecutive(
                ['/testhost1_com/1.json'],
                ['/testhost1_com/2.json'],
                ['/testhost2_com/3.json'],
                ['/testhost2_com/4.json'],
                ['/badbundles_com/1.json'],
                ['/complete_corrupted_bundle_com/1.json']
            )
            ->will($this->returnCallback(array($this, 'returnReadBundleCallback')));

        $monitor = ImportBundles::run($storageMock, $clickHouseConnectionMock);

        $monitored = $monitor->getMarkersByGroups();

        // print_r($monitored);

        // Check Success
        $this->assertEquals("2", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost1_com/1.json"]["value"]);
        $this->assertEquals("beacons_in_bundle", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost1_com/1.json"]["valueDescriptor"]);

        $this->assertEquals("2", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost1_com/2.json"]["value"]);
        $this->assertEquals("beacons_in_bundle", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost1_com/2.json"]["valueDescriptor"]);

        $this->assertEquals("2", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost2_com/3.json"]["value"]);
        $this->assertEquals("beacons_in_bundle", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost2_com/3.json"]["valueDescriptor"]);

        $this->assertEquals("2", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost2_com/4.json"]["value"]);
        $this->assertEquals("beacons_in_bundle", $monitored["App\BasicRum\Workflows\ImportBundles"]["bundle_read_success"]["/testhost2_com/4.json"]["valueDescriptor"]);
    }

    public function returnReadBundleCallback() : string
    {
        $args = func_get_args();

        $beaconsBundlePath = $args[0];

        if ('/badbundles_com/1.json' === $beaconsBundlePath) {
            return implode(
                "\n",
                [
                    "{}}",
                    "malformed_json"
                ]
            );
        }

        if ('/complete_corrupted_bundle_com/1.json' === $beaconsBundlePath) {
            return '';
        }

        $testBeacon = "{\"c_e\":\"kssyn099\",\"c_tti_m\":\"raf\",\"early\":\"1\",\"rt_start\":\"navigation\",\"rt_bmr\":\"243,607,445,76,76,54,45,45,4\",\"rt_tstart\":\"1629984420621\",\"rt_bstart\":\"1629984421503\",\"rt_blstart\":\"1629984420865\",\"rt_end\":\"1629984420621\",\"t_other\":\"boomerang|2,boomr_fb|882,boomr_ld|244,boomr_lat|638\",\"rt_tt\":\"0\",\"rt_obo\":\"0\",\"nt_nav_st\":\"1629984420621\",\"nt_fet_st\":\"1629984420621\",\"nt_dns_st\":\"1629984420621\",\"nt_dns_end\":\"1629984420621\",\"nt_con_st\":\"1629984420621\",\"nt_con_end\":\"1629984420621\",\"nt_req_st\":\"1629984420776\",\"nt_res_st\":\"1629984420848\",\"nt_res_end\":\"1629984420884\",\"nt_domloading\":\"1629984420860\",\"nt_domint\":\"1629984421023\",\"nt_domcontloaded_st\":\"1629984421023\",\"nt_domcontloaded_end\":\"1629984421031\",\"nt_red_cnt\":\"0\",\"nt_nav_type\":\"0\",\"u\":\"https:\\\/\\\/test.com\\\/map\\\/object\\\/view\\\/google\\\/\",\"r\":\"https:\\\/\\\/www.google.com\",\"v\":\"1.0.0\",\"sv\":\"15\",\"sm\":\"p\",\"rt_si\":\"7cadbbbe-55cf-4f31-958b-5d1801aff1f4-qyg811\",\"rt_ss\":\"1629984420621\",\"rt_sl\":\"0\",\"vis_st\":\"visible\",\"ua_plt\":\"iPhone\",\"ua_vnd\":\"Apple Computer, Inc.\",\"pid\":\"lo90475f\",\"n\":\"1\",\"c_t_fps\":\"011655\",\"c_tti_vr\":\"410\",\"c_f\":\"36\",\"c_f_d\":\"488\",\"c_f_m\":\"1\",\"c_f_l\":\"1\",\"c_f_s\":\"kssyn0kn\",\"dom_res\":\"39\",\"dom_doms\":\"16\",\"mem_lsln\":\"7\",\"mem_ssln\":\"1\",\"mem_lssz\":\"214\",\"mem_sssz\":\"17\",\"scr_xy\":\"414x896\",\"scr_bpp\":\"32\\\/32\",\"scr_dpx\":\"2\",\"scr_mtp\":\"5\",\"dom_ln\":\"533\",\"dom_sz\":\"108005\",\"dom_ck\":\"471\",\"dom_img\":\"37\",\"dom_img_uniq\":\"15\",\"dom_script\":\"34\",\"dom_script_ext\":\"17\",\"dom_iframe\":\"4\",\"dom_iframe_ext\":\"3\",\"dom_link\":\"33\",\"dom_link_css\":\"1\",\"sb\":\"1\",\"user_agent\":\"Mozilla\\\/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit\\\/605.1.15 (KHTML, like Gecko) Version\\\/14.1.2 Mobile\\\/15E148 Safari\\\/604.1\",\"created_at\":\"2021-08-26 13:27:02\"}";

        return implode(
            "\n",
            [
                $testBeacon,
                $testBeacon
            ]
        );
    }

}
