<?php

namespace  App\Tests\BasicRum\Beacon\Importer\Process;

use PHPUnit\Framework\TestCase;
use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;

class Beacon extends TestCase
{

    /**
     * @group import
     */
    public function testMissingProcessIdConstraints()
    {
        $beacons = [
            [
                0 => "1554048464",
                1 => '{"mob_ct":"wifi","mob_etype":"4g","mob_lm":"Infinity","mob_dl":"1.55","mob_rtt":"150","guid":"b2122753-c94e-4b36-a25f-f5c9a744e2a2_1554055661692","tp_ga_clientid":"866725451.1554055662","nt_nav_st":"1554055659318","nt_fet_st":"1554055659558","nt_dns_st":"1554055659574","nt_dns_end":"1554055659615","nt_con_st":"1554055659615","nt_con_end":"1554055659846","nt_req_st":"1554055659851","nt_res_st":"1554055660400","nt_res_end":"1554055660534","nt_domloading":"1554055660497","nt_domint":"1554055661226","nt_domcontloaded_st":"1554055661283","nt_domcontloaded_end":"1554055661283","nt_domcomp":"1554055661288","nt_load_st":"1554055661288","nt_load_end":"1554055661318","nt_enc_size":"33606","nt_dec_size":"147125","nt_trn_size":"34288","nt_protocol":"h2","nt_first_paint":"1554055661120","nt_red_cnt":"0","nt_nav_type":"0","restiming":"{\"https:\/\/\":{\"www.\":{\"darvart.de\/\":{\"holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB\":\"6,xs,u2,eu,ep,,89,89,74*1pxi,iy,2flb\",\"skin\/frontend\/darvart\/default\/images\/\":{\"darvart-navy-logo.png\":\"*028,28,k,5,2s,2s|1yu,1v,1s,8*12h5,33\",\"icon_sprite@2x.png\":\"413l,17,13,9*18m7,36\",\"opc-ajax-loader.gif\":\"413t,12,z,6*15sj,34\"},\"media\/\":{\"catalog\/product\/cache\/1\/\":{\"image\/\":{\"1800x\/040ec09b1e35df139433887a97daa66f\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"1yw,3w,32,26*1sd5,35\",\"9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*0bc,94,6p,g,ms,ic|1yv,1q,11,a*1sd5,5w\"},\"thumbnail\/132x164\/9df78eab33525d08d6e5fb8d27136e95\/d\/a\/darvena-papijonka-dzhak-01.jpg\":\"*02a,1u,id,f,4k,3o|1yx,3n,3k,26*12or,28\"},\"js\/1bd4352afe41b2982b029f9c706b528e.js\":\"21ii,2w,1f,e*12jp2,7c,6x6t*24\",\"favicon\/default\/fvicon.png\":\"01mk,1j,14,f*19m,2v\"},\"cdn-cgi\/scripts\/5c5dd728\/cloudflare-static\/email-decode.min.js\":\"312y,17,12,6*1i7,48,g8\"},\"google\":{\"tagmanager.com\/gtag\/js?id=AW-850221740\":\"21ig,7i*25\",\"-analytics.com\/\":{\"analytics.js\":\"323k,3v,3p,30,3n,1d,1c,1c,8*1djb,-dgf,kij*25\",\"r\/collect?v=1&_v=j73&aip=1&a=1893443103&t=pageview&_s=1&dl=https%3A%2F%2Fwww.darvart.de%2Fholzfliege-jack.html%3Fgclid%3DCj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB&dr=https%3A%2F%2Fwww.google.com%2F&ul=de&de=UTF-8&dt=Holzfliege%20%22Jack%22&sd=24-bit&sr=360x640&vp=360x559&je=0&_u=YEBAAEABAAAAg~&jid=1361856714&gjid=1003687476&cid=866725451.1554055662&tid=UA-89019502-1&_gid=1286799559.1554055662&_r=1&z=372481635\":\"12c3,w\"},\"adservices.com\/pagead\/conversion_async.js\":\"329c,12,y,9*16to,-6nz,bb4*25\"}},\"ajax.cloudflare.com\/cdn-cgi\/scripts\/a2bd7673\/cloudflare-static\/rocket-loader.min.js\":\"3131,6b\"}}","pt_fp":"1802","pt_fcp":"1802","u":"https:\/\/www.darvart.de\/holzfliege-jack.html?gclid=Cj0KCQjwyoHlBRCNARIsAFjKJ6AzDe0jLjUIkeqt5GqmEuCwS06THqi76_PnPrStUeiBEBGshrrtS8UaAnVYEALw_wcB","v":"%boomerang_version%","vis_st":"visible","ua_plt":"Linux armv7l","ua_vnd":"Google Inc.","if":"","sb":"1","user_agent":"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36","created_at":"2019-03-31 18:07:44"}'
            ]
        ];

        $decompressor = new ResourceTimingDecompressor_v_0_3_4();

        foreach ($beacons as $key => $item)
        {
            $tempArray = json_decode($item[1], true);
            $resourceTimingsData = $decompressor->decompressResources(json_decode($tempArray['restiming'], true));
            $tempArray['restiming'] = $resourceTimingsData;
            $beacons[$key][1] = json_encode($tempArray);
        }

        // print_r($beacons); exit();

        // return $beacons;

        $beacon = new \App\BasicRum\Beacon\Importer\Process\Beacon();

        $result = $beacon->extract($beacons);

        //var_dump($result);

        $this->assertEquals(
            'missing',
            $result[0]['process_id']
        );
    }

}