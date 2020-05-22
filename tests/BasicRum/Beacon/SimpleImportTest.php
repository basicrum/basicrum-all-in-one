<?php

namespace  App\Tests\BasicRum\Beacon;

use App\Tests\BasicRum\NoFixturesTestCase;

use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Beacon\Importer\Process;
use Doctrine\Bundle\DoctrineBundle\Registry;

class SimpleImportTest extends NoFixturesTestCase
{


    /**
     * @return Registry $doctrine
     */
    private function _getDoctrine() : Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    /**
     * @group import
     */
    public function testSimpleImport()
    {
        $reader = $this
            ->getMockBuilder(Process\Reader\MonolithCatcher::class)
            ->setConstructorArgs(['dummy'])
            ->getMock();

        $reader
            ->expects($this->atLeastOnce())
            ->method('read')
            ->will($this->returnValue(
                [
                    [
                        0 => "1554048464",
                        1 => '{"mob_ct":"wifi","mob_etype":"4g","mob_lm":"Infinity","mob_dl":"1.55","mob_rtt":"150","guid":"b2122753-c94e-4b36-a25f-f5c9a744e2a2_1554055661692","tp_ga_clientid":"866725451.1554055662","nt_nav_st":"1554055659318","nt_fet_st":"1554055659558","nt_dns_st":"1554055659574","nt_dns_end":"1554055659615","nt_con_st":"1554055659615","nt_con_end":"1554055659846","nt_req_st":"1554055659851","nt_res_st":"1554055660400","nt_res_end":"1554055660534","nt_domloading":"1554055660497","nt_domint":"1554055661226","nt_domcontloaded_st":"1554055661283","nt_domcontloaded_end":"1554055661283","nt_domcomp":"1554055661288","nt_load_st":"1554055661288","nt_load_end":"1554055661318","nt_enc_size":"33606","nt_dec_size":"147125","nt_trn_size":"34288","nt_protocol":"h2","nt_first_paint":"1554055661120","nt_red_cnt":"0","nt_nav_type":"0","restiming":"{\"https:\/\/\":{\"www.\":{\"darvart.de\/\":{\"holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB\":\"6,xs,u2,eu,ep,,89,89,74*1pxi,iy,2flb\",\"skin\/frontend\/darvart\/default\/images\/\":{\"darvart-navy-logo.png\":\"*028,28,k,5,2s,2s|1yu,1v,1s,8*12h5,33\",\"icon_sprite@2x.png\":\"413l,17,13,9*18m7,36\",\"opc-ajax-loader.gif\":\"413t,12,z,6*15sj,34\"},\"media\/\":{\"catalog\/product\/cache\/1\/\":{\"image\/\":{\"1800x\/040ec09b1e35df139433887a97daa66f\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"1yw,3w,32,26*1sd5,35\",\"9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*0bc,94,6p,g,ms,ic|1yv,1q,11,a*1sd5,5w\"},\"thumbnail\/132x164\/9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*02a,1u,id,f,4k,3o|1yx,3n,3k,26*12or,28\"},\"js\/1bd4352afe41b2982b029f9c706b528e.js\":\"21ii,2w,1f,e*12jp2,7c,6x6t*24\",\"favicon\/default\/fvicon.png\":\"01mk,1j,14,f*19m,2v\"},\"cdn-cgi\/scripts\/5c5dd728\/cloudflare-static\/email-decode.min.js\":\"312y,17,12,6*1i7,48,g8\"},\"google\":{\"tagmanager.com\/gtag\/js?id=AW-850221740\":\"21ig,7i*25\",\"-analytics.com\/\":{\"analytics.js\":\"323k,3v,3p,30,3n,1d,1c,1c,8*1djb,-dgf,kij*25\",\"r\/collect?v=1&_v=j73&aip=1&a=1893443103&t=pageview&_s=1&dl=https%3A%2F%2Fwww.darvart.de%2Fholzfliege-jack.html%3Fgclid%3DCj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB&dr=https%3A%2F%2Fwww.google.com%2F&ul=de&de=UTF-8&dt=Holzfliege%20%22Jack%22&sd=24-bit&sr=360x640&vp=360x559&je=0&_u=YEBAAEABAAAAg~&jid=1361856714&gjid=1003687476&cid=866725451.1554055662&tid=UA-89019502-1&_gid=1286799559.1554055662&_r=1&z=372481635\":\"12c3,w\"},\"adservices.com\/pagead\/conversion_async.js\":\"329c,12,y,9*16to,-6nz,bb4*25\"}},\"ajax.cloudflare.com\/cdn-cgi\/scripts\/a2bd7673\/cloudflare-static\/rocket-loader.min.js\":\"3131,6b\"}}","pt_fp":"1802","pt_fcp":"1802","u":"https:\/\/www.darvart.de\/holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB","v":"%boomerang_version%","vis_st":"visible","ua_plt":"Linux armv7l","ua_vnd":"Google Inc.","pid":"mpedg450","if":"","sb":"1","user_agent":"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36","created_at":"2019-03-31 18:07:44"}'
                    ],
                    [
                        0 => "1554048802",
                        1 => '{"mob_ct":"wifi","mob_etype":"4g","mob_lm":"Infinity","mob_dl":"1.65","mob_rtt":"200","guid":"b2122753-c94e-4b36-a25f-f5c9a744e2a2_1554055661692","tp_ga_clientid":"866725451.1554055662","nt_nav_st":"1554055997060","nt_fet_st":"1554055997158","nt_dns_st":"1554055997158","nt_dns_end":"1554055997158","nt_con_st":"1554055997158","nt_con_end":"1554055997158","nt_req_st":"1554055997175","nt_res_st":"1554055997735","nt_res_end":"1554055997849","nt_domloading":"1554055997765","nt_domint":"1554055998599","nt_domcontloaded_st":"1554055998667","nt_domcontloaded_end":"1554055998667","nt_domcomp":"1554055998675","nt_load_st":"1554055998675","nt_load_end":"1554055998738","nt_enc_size":"33605","nt_dec_size":"147126","nt_trn_size":"33931","nt_protocol":"h2","nt_first_paint":"1554055998341","nt_red_cnt":"0","nt_nav_type":"0","restiming":"{\"https:\/\/\":{\"www.\":{\"darvart.de\/\":{\"holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB\":\"6,lx,ir,37,2r,,2r,2r,2r*1pxh,92,2fld\",\"skin\/frontend\/darvart\/default\/images\/\":{\"darvart-navy-logo.png\":\"*028,28,k,5,2s,2s|1m1,c,a,7*12h5,_\",\"icon_sprite@2x.png\":\"4og,9,6,3*18m7,_\",\"opc-ajax-loader.gif\":\"411h,e,8,4*15sj,_\"},\"media\/\":{\"catalog\/product\/cache\/1\/\":{\"image\/\":{\"1800x\/040ec09b1e35df139433887a97daa66f\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"1nh,z,m,6*1sd5,_\",\"9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*0bc,94,6p,g,ms,ic|1ng,z,n,4*1sd5,_\"},\"thumbnail\/132x164\/9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*02a,1u,id,f,4k,3o|1nj,u,n,6*12or,_\"},\"js\/1bd4352afe41b2982b029f9c706b528e.js\":\"218f,31,q,b*12jp2,_,6x6t*24\"},\"cdn-cgi\/scripts\/5c5dd728\/cloudflare-static\/email-decode.min.js\":\"3nk,19,m,b*1i7,_,g8\"},\"google\":{\"tagmanager.com\/gtag\/js?id=AW-850221740\":\"218b,2p*25\",\"-analytics.com\/\":{\"analytics.js\":\"31r8,1g,v,m*1djb,_,kij*25\",\"r\/collect?v=1&_v=j73&aip=1&a=1587260&t=pageview&_s=1&dl=https%3A%2F%2Fwww.darvart.de%2Fholzfliege-jack.html%3Fgclid%3DCj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB&ul=de&de=UTF-8&dt=Holzfliege%20%22Jack%22&sd=24-bit&sr=360x640&vp=360x559&je=0&_u=QACAAEABAAAAg~&jid=28309293&gjid=1424680106&cid=866725451.1554055662&tid=UA-89019502-1&_gid=1286799559.1554055662&_r=1&z=471228299\":\"124d,3m\",\"collect?...\":\"1251,2o|128c,10\"},\"adservices.com\/pagead\/conversion_async.js\":\"3272,15,y,i*16to,_,bb4*25\"}},\"ajax.cloudflare.com\/cdn-cgi\/scripts\/a2bd7673\/cloudflare-static\/rocket-loader.min.js\":\"3no,1f\"}}","pt_fp":"1281","pt_fcp":"1281","u":"https:\/\/www.darvart.de\/holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB","v":"%boomerang_version%","vis_st":"visible","ua_plt":"Linux armv7l","ua_vnd":"Google Inc.","pid":"q0pco4fi","if":"","sb":"1","user_agent":"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36","created_at":"2019-03-31 18:13:22"}'
                    ],
                    [
                        0 => "1554049387",
                        1 => '{"mob_ct":"wifi","mob_etype":"4g","mob_lm":"Infinity","mob_dl":"4.8","mob_rtt":"100","guid":"6b1ad1ce-1df6-41ea-992d-04003f376a07_1554056434499","u":"https:\/\/www.darvart.de\/holzfliege-robert.html?gclid=EAIaIQobChMI_uzt5P-s4QIVXcayCh2mQwerEAQYBCABEgK5PvD_BwE","v":"%boomerang_version%","vis_st":"visible","ua_plt":"Linux armv7l","ua_vnd":"Google Inc.","pid":"cykloulz","if":"","nt_nav_st":"1554056555373","nt_fet_st":"1554056555396","nt_dns_st":"1554056555396","nt_dns_end":"1554056555396","nt_con_st":"1554056555396","nt_con_end":"1554056555396","nt_req_st":"1554056555405","nt_res_st":"1554056556131","nt_res_end":"1554056556200","nt_domloading":"1554056556276","nt_domint":"1554056556475","nt_domcontloaded_st":"1554056556504","nt_domcontloaded_end":"1554056556504","nt_domcomp":"1554056556962","nt_load_st":"1554056556964","nt_load_end":"1554056556992","nt_enc_size":"35031","nt_dec_size":"147436","nt_trn_size":"35263","nt_protocol":"h2","nt_first_paint":"1554056556441","nt_red_cnt":"0","nt_nav_type":"0","restiming":"{\"https:\/\/\":{\"www.\":{\"darvart.de\/\":{\"holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB\":\"6,xs,u2,eu,ep,,89,89,74*1pxi,iy,2flb\",\"skin\/frontend\/darvart\/default\/images\/\":{\"darvart-navy-logo.png\":\"*028,28,k,5,2s,2s|1yu,1v,1s,8*12h5,33\",\"icon_sprite@2x.png\":\"413l,17,13,9*18m7,36\",\"opc-ajax-loader.gif\":\"413t,12,z,6*15sj,34\"},\"media\/\":{\"catalog\/product\/cache\/1\/\":{\"image\/\":{\"1800x\/040ec09b1e35df139433887a97daa66f\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"1yw,3w,32,26*1sd5,35\",\"9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*0bc,94,6p,g,ms,ic|1yv,1q,11,a*1sd5,5w\"},\"thumbnail\/132x164\/9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*02a,1u,id,f,4k,3o|1yx,3n,3k,26*12or,28\"},\"js\/1bd4352afe41b2982b029f9c706b528e.js\":\"21ii,2w,1f,e*12jp2,7c,6x6t*24\",\"favicon\/default\/fvicon.png\":\"01mk,1j,14,f*19m,2v\"},\"cdn-cgi\/scripts\/5c5dd728\/cloudflare-static\/email-decode.min.js\":\"312y,17,12,6*1i7,48,g8\"},\"google\":{\"tagmanager.com\/gtag\/js?id=AW-850221740\":\"21ig,7i*25\",\"-analytics.com\/\":{\"analytics.js\":\"323k,3v,3p,30,3n,1d,1c,1c,8*1djb,-dgf,kij*25\",\"r\/collect?v=1&_v=j73&aip=1&a=1893443103&t=pageview&_s=1&dl=https%3A%2F%2Fwww.darvart.de%2Fholzfliege-jack.html%3Fgclid%3DCj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB&dr=https%3A%2F%2Fwww.google.com%2F&ul=de&de=UTF-8&dt=Holzfliege%20%22Jack%22&sd=24-bit&sr=360x640&vp=360x559&je=0&_u=YEBAAEABAAAAg~&jid=1361856714&gjid=1003687476&cid=866725451.1554055662&tid=UA-89019502-1&_gid=1286799559.1554055662&_r=1&z=372481635\":\"12c3,w\"},\"adservices.com\/pagead\/conversion_async.js\":\"329c,12,y,9*16to,-6nz,bb4*25\"}},\"ajax.cloudflare.com\/cdn-cgi\/scripts\/a2bd7673\/cloudflare-static\/rocket-loader.min.js\":\"3131,6b\"}}","sb":"1","user_agent":"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36","created_at":"2019-03-31 18:23:07"}'
                    ],
                ]
            ));

        $process = new Process($this->_getDoctrine());
        $c = $process->runImport($reader, 1);


        //<input type="hidden" name="segments[1][data_requirements][internal_data][data_field][data_flavor][data_rows][fields]" value="page_view_id" />
        $input = [
            'global' => [
                'data_requirements' => [
                    'period' => [
                        'type'  => 'fixed',
                        'start' => '03/31/2019',
                        'end'   => '04/01/2019',
                    ]
                ]
            ],
            'segments' => [
                1 => [
                    'data_requirements' => [
                        'internal_data' => [
                            'data_field' => [
                                'data_flavor' => [
                                    'data_rows' => [
                                        'fields' => [
                                            'page_view_id',
                                            'first_paint'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        /** @var DiagramOrchestrator $diagramOrchestrator */
        $diagramOrchestrator = self::bootKernel()->getContainer()->get(DiagramOrchestrator::class);
        $res = $diagramOrchestrator->load($input)->process();

        $this->assertEquals(
            [
                1 => [
                    '2019-03-31 00:00:00' =>
                        [
                            'data_rows' => [
                                [
                                    'page_view_id'     => 1,
                                    'first_paint'      => 1802
                                ],
                                [
                                    'page_view_id'     => 2,
                                    'first_paint'      => 1281
                                ],
                                [
                                    'page_view_id'     => 3,
                                    'first_paint'      => 1068
                                ]
                            ]
                        ]
                ]
            ],
            $res
        );
    }

}
