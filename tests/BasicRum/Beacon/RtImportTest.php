<?php

namespace  App\Tests\BasicRum\Beacon;

use App\BasicRum\Beacon\Importer\Process;
use App\Entity\RumDataFlat;
use App\Tests\BasicRum\NoFixturesTestCase;
use Doctrine\Bundle\DoctrineBundle\Registry;

class RtImportTest extends NoFixturesTestCase
{
    /**
     * @return Registry $doctrine
     */
    private function _getDoctrine(): Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    /**
     * @group import
     */
    public function testRtImport()
    {
        $reader = $this
            ->getMockBuilder(Process\Reader\MonolithCatcher::class)
            ->setConstructorArgs(['dummy'])
            ->getMock();

        $reader
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(
                [
                    [ // initial beacon
                        0 => '1554048464',
                        1 => '{"pt_hid":"1","rt_start":"navigation","rt_bmr":"30,57,52,51","rt_tstart":"1593518881947","rt_bstart":"1593518882036","rt_end":"1593518882962","t_resp":"7","t_page":"1008","t_done":"1015","t_other":"t_domloaded|199,boomerang|928,boomr_fb|89","rt_tt":"1511","rt_obo":"0","nt_nav_st":"1593518881947","nt_fet_st":"1593518881948","nt_dns_st":"1593518881948","nt_dns_end":"1593518881948","nt_con_st":"1593518881948","nt_con_end":"1593518881948","nt_req_st":"1593518881952","nt_res_st":"1593518881954","nt_res_end":"1593518881955","nt_domloading":"1593518881971","nt_domint":"1593518882145","nt_domcontloaded_st":"1593518882145","nt_domcontloaded_end":"1593518882145","nt_domcomp":"1593518882958","nt_load_st":"1593518882958","nt_load_end":"1593518882961","nt_enc_size":"4642","nt_dec_size":"4642","nt_trn_size":"0","nt_red_cnt":"0","nt_nav_type":"2","restiming":"{\"http\":{\":\/\/tests.lc\/boomerangjs\/\":{\"|\":\"6,8,7,5,2,,2,2,2*13ky,_\",\"vendor\/\":{\"boo\":{\"tstrap\/bootstrap.min.css\":\"2u,4,2,2*13f2z,_*44\",\"merang\/plugins\/\":{\"r\":{\"t.js\":\"3u,1k,1g,1f*118aj,_*24\",\"estiming.js\":\"3u,1p,1o,1n*11c0f,_*24\"},\"navtiming.js\":\"3u,1k,1g,1g*1c8a,_*24\",\"painttiming.js\":\"3u,1p,1n,1n*14qc,_*24\"}},\"jquery\/jquery-\":{\"3.4.1.min.js\":\"3u,1p,1o,1n*11w0h,_*24\",\"migrate-1.4.1.min.js\":\"3u,1u,1s,1s*17rc,_*24\"}}},\"s:\/\/\":{\"cdnjs.cloudflare.com\/ajax\/libs\/foundation\/6.6.3\/\":{\"css\/foundation.min.css\":\"2u,3,2,2*1c6s,_,2i59*44\",\"js\/foundation.min.js\":\"3u,1v,1t,1s*1rnv,_,34tu*24\"},\"upload.wikimedia.org\/wikipedia\/commons\/thumb\/9\/96\/Flumet.jpg\/1200px-Flumet.jpg\":\"*0p0,xc,15,5n|1u,20,1z,1y*17k4r,_*30\"}}}","servertiming":"[[\"cache\",\"hit-local\"]]","u":"http:\/\/tests.lc\/boomerangjs\/","v":"%boomerang_version%","sm":"i","rt_si":"15864724-be22-44e5-8d19-4560365df411-qcqn0w","rt_ss":"1593518864467","rt_sl":"2","vis_st":"hidden","ua_plt":"Linux x86_64","ua_vnd":"Google Inc.","pid":"lbcrlkdx","n":"1","sb":"1","user_agent":"Mozilla\/5.0 (X11; Linux x86_64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/83.0.4103.116 Safari\/537.36","created_at":"2020-06-30 15:08:03"}',
                    ],
                    [ // XHR beacon
                        0 => '1554048802',
                        1 => '{"u":"http:\/\/tests.lc\/boomerangjs\/index.php","v":"%boomerang_version%","sm":"i","rt_si":"15864724-be22-44e5-8d19-4560365df411-qcu2fp","vis_st":"hidden","ua_plt":"Linux x86_64","ua_vnd":"Google Inc.","pid":"uo589ilh","n":"2","restiming":"{\"http:\/\/tests.lc\/boomerangjs\/index.php\":\"5nl,2,2,1*15,_\"}","rt_start":"manual","rt_tstart":"1593678805608","rt_nstart":"1593678804759","rt_bstart":"1593678804821","rt_end":"1593678805631","t_resp":"2","t_page":"21","t_done":"23","http_initiator":"xhr","rt_tt":"877","rt_obo":"0","nt_fet_st":"1593678805608","nt_dns_st":"1593678805608","nt_dns_end":"1593678805608","nt_con_st":"1593678805608","nt_con_end":"1593678805608","nt_req_st":"1593678805609","nt_res_st":"1593678805609","nt_res_end":"1593678805610","nt_domint":"1593678805630","nt_load_st":"1593678805631","nt_load_end":"1593678805631","pgu":"http:\/\/tests.lc\/boomerangjs\/","rt_ss":"1593678804759","rt_sl":"2","sb":"1","user_agent":"Mozilla\/5.0 (X11; Linux x86_64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/83.0.4103.116 Safari\/537.36","created_at":"2020-07-02 11:33:25"}',
                    ],
                    [ // rt_quit beacon
                        0 => '1554049387',
                        1 => '{"u":"http:\/\/tests.lc\/boomerangjs\/index3.html","v":"%boomerang_version%","sm":"i","rt_si":"5f8123cc-187e-41c8-b191-468b04656eac-qcgzws","vis_st":"visible","ua_plt":"Linux x86_64","ua_vnd":"Google Inc.","pid":"9xzhm5u5","n":"2","rt_tstart":"1593069004366","rt_bstart":"1593069004422","rt_end":"1593069004443","t_done":"77","rt_tt":"77","rt_obo":"0","rt_quit":"","rt_ss":"1593069004366","rt_sl":"1","sb":"1","user_agent":"Mozilla\/5.0 (X11; Linux x86_64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/83.0.4103.116 Safari\/537.36","created_at":"2020-06-25 10:10:07"}',
                    ],
                ]
            );

        $process = new Process($this->_getDoctrine());
        $c = $process->runImport($reader, 1);

        $em = $this->_getDoctrine()->getManager();
        $rumDataFlat = $em->getRepository(RumDataFlat::class)->findAll();

        $array = [
            // initial beacon
            [
                'rumDataId' => 1,
                'rt_quit' => 0,
                'http_initiator' => null,
                't_done' => 1015,
                't_page' => 1008,
                't_resp' => 7,
                't_load' => 0,
                'rt_tstart' => 1593518881947,
                'rt_end' => 1593518882962,
                'rt_si' => '15864724-be22-44e5-8d19-4560365df411-qcqn0w',
            ],
            // xhr beacon
            [
                'rumDataId' => 2,
                'rt_quit' => 0,
                'http_initiator' => 'xhr',
                't_done' => 23,
                't_page' => 21,
                't_resp' => 2,
                't_load' => 0,
                'rt_tstart' => 1593678805608,
                'rt_end' => 1593678805631,
                'rt_si' => '15864724-be22-44e5-8d19-4560365df411-qcu2fp',
            ],
            // rt_quit beacon
            [
                'rumDataId' => 3,
                'rt_quit' => 1,
                'http_initiator' => null,
                't_done' => 77,
                't_page' => 0,
                't_resp' => 0,
                't_load' => 0,
                'rt_tstart' => 1593069004366,
                'rt_end' => 1593069004443,
                'rt_si' => '5f8123cc-187e-41c8-b191-468b04656eac-qcgzws',
            ],
        ];

        $testArray = [
            // initial beacon
            [
                'rumDataId' => $rumDataFlat[0]->getRumDataId(),
                'rt_quit' => $rumDataFlat[0]->getRtQuit(),
                'http_initiator' => $rumDataFlat[0]->getHttpInitiator(),
                't_done' => $rumDataFlat[0]->getTdone(),
                't_page' => $rumDataFlat[0]->getTpage(),
                't_resp' => $rumDataFlat[0]->getTresp(),
                't_load' => $rumDataFlat[0]->getTload(),
                'rt_tstart' => $rumDataFlat[0]->getRtTstart(),
                'rt_end' => $rumDataFlat[0]->getRtEnd(),
                'rt_si' => $rumDataFlat[0]->getRtsi(),
            ],
            // xhr beacon
            [
                'rumDataId' => $rumDataFlat[1]->getRumDataId(),
                'rt_quit' => $rumDataFlat[1]->getRtQuit(),
                'http_initiator' => $rumDataFlat[1]->getHttpInitiator(),
                't_done' => $rumDataFlat[1]->getTdone(),
                't_page' => $rumDataFlat[1]->getTpage(),
                't_resp' => $rumDataFlat[1]->getTresp(),
                't_load' => $rumDataFlat[1]->getTload(),
                'rt_tstart' => $rumDataFlat[1]->getRtTstart(),
                'rt_end' => $rumDataFlat[1]->getRtEnd(),
                'rt_si' => $rumDataFlat[1]->getRtsi(),
            ],
            // rt_quit beacon
            [
                'rumDataId' => $rumDataFlat[2]->getRumDataId(),
                'rt_quit' => $rumDataFlat[2]->getRtQuit(),
                'http_initiator' => $rumDataFlat[2]->getHttpInitiator(),
                't_done' => $rumDataFlat[2]->getTdone(),
                't_page' => $rumDataFlat[2]->getTpage(),
                't_resp' => $rumDataFlat[2]->getTresp(),
                't_load' => $rumDataFlat[2]->getTload(),
                'rt_tstart' => $rumDataFlat[2]->getRtTstart(),
                'rt_end' => $rumDataFlat[2]->getRtEnd(),
                'rt_si' => $rumDataFlat[2]->getRtsi(),
            ],
        ];

        $this->assertEquals($array, $testArray);
    }
}
