<?php

namespace  App\Tests\BasicRum\Beacon\Importer\Process;

use PHPUnit\Framework\TestCase;

class BeaconTest extends TestCase
{
    /**
     * @group import
     */
    public function testMissingProcessIdConstraints()
    {
        $beacons = [
            [
                0 => '1554048464',
                1 => '{"mob_ct":"wifi","mob_etype":"4g","mob_lm":"Infinity","mob_dl":"1.65","mob_rtt":"200","guid":"b2122753-c94e-4b36-a25f-f5c9a744e2a2_1554055661692","tp_ga_clientid":"866725451.1554055662","nt_nav_st":"1554055997060","nt_fet_st":"1554055997158","nt_dns_st":"1554055997158","nt_dns_end":"1554055997158","nt_con_st":"1554055997158","nt_con_end":"1554055997158","nt_req_st":"1554055997175","nt_res_st":"1554055997735","nt_res_end":"1554055997849","nt_domloading":"1554055997765","nt_domint":"1554055998599","nt_domcontloaded_st":"1554055998667","nt_domcontloaded_end":"1554055998667","nt_domcomp":"1554055998675","nt_load_st":"1554055998675","nt_load_end":"1554055998738","nt_enc_size":"33605","nt_dec_size":"147126","nt_trn_size":"33931","nt_protocol":"h2","nt_first_paint":"1554055998341","nt_red_cnt":"0","nt_nav_type":"0","restiming":"{\"https:\/\/\":{\"www.\":{\"darvart.de\/\":{\"holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB\":\"6,lx,ir,37,2r,,2r,2r,2r*1pxh,92,2fld\",\"skin\/frontend\/darvart\/default\/images\/\":{\"darvart-navy-logo.png\":\"*028,28,k,5,2s,2s|1m1,c,a,7*12h5,_\",\"icon_sprite@2x.png\":\"4og,9,6,3*18m7,_\",\"opc-ajax-loader.gif\":\"411h,e,8,4*15sj,_\"},\"media\/\":{\"catalog\/product\/cache\/1\/\":{\"image\/\":{\"1800x\/040ec09b1e35df139433887a97daa66f\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"1nh,z,m,6*1sd5,_\",\"9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*0bc,94,6p,g,ms,ic|1ng,z,n,4*1sd5,_\"},\"thumbnail\/132x164\/9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*02a,1u,id,f,4k,3o|1nj,u,n,6*12or,_\"},\"js\/1bd4352afe41b2982b029f9c706b528e.js\":\"218f,31,q,b*12jp2,_,6x6t*24\"},\"cdn-cgi\/scripts\/5c5dd728\/cloudflare-static\/email-decode.min.js\":\"3nk,19,m,b*1i7,_,g8\"},\"google\":{\"tagmanager.com\/gtag\/js?id=AW-850221740\":\"218b,2p*25\",\"-analytics.com\/\":{\"analytics.js\":\"31r8,1g,v,m*1djb,_,kij*25\",\"r\/collect?v=1&_v=j73&aip=1&a=1587260&t=pageview&_s=1&dl=https%3A%2F%2Fwww.darvart.de%2Fholzfliege-jack.html%3Fgclid%3DCj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB&ul=de&de=UTF-8&dt=Holzfliege%20%22Jack%22&sd=24-bit&sr=360x640&vp=360x559&je=0&_u=QACAAEABAAAAg~&jid=28309293&gjid=1424680106&cid=866725451.1554055662&tid=UA-89019502-1&_gid=1286799559.1554055662&_r=1&z=471228299\":\"124d,3m\",\"collect?...\":\"1251,2o|128c,10\"},\"adservices.com\/pagead\/conversion_async.js\":\"3272,15,y,i*16to,_,bb4*25\"}},\"ajax.cloudflare.com\/cdn-cgi\/scripts\/a2bd7673\/cloudflare-static\/rocket-loader.min.js\":\"3no,1f\"}}","pt_fp":"1281","pt_fcp":"1281","u":"https:\/\/www.darvart.de\/holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB","v":"%boomerang_version%","vis_st":"visible","ua_plt":"Linux armv7l","ua_vnd":"Google Inc.","if":"","sb":"1","user_agent":"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36","created_at":"2019-03-31 18:13:22"}',
            ],
        ];

        $beacon = new \App\BasicRum\Beacon\Importer\Process\Beacon();

        $result = $beacon->extract($beacons);

        $this->assertEquals(
            'missing',
            $result[0]['process_id']
        );
    }
}
