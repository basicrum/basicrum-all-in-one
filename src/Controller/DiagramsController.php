<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\BasicRum\WaterfallSvgRenderer;
use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;

class DiagramsController extends Controller
{

    /**
     * @Route("/diagrams/release/compare", name="diagrams_release_compare")
     */
    public function releaseCompare()
    {
        $prevPeriod = '{"0":"0.00","200":"0.33","400":"0.66","600":"1.16","800":"1.66","1000":"2.11","1200":"2.87","1400":"3.79","1600":"4.02","1800":"4.63","2000":"5.06","2200":"5.47","2400":"5.81","2600":"6.09","2800":"5.93","3000":"5.67","3200":"5.33","3400":"4.95","3600":"4.46","3800":"3.61","4000":"3.44","4200":"3.10","4400":"2.59","4600":"2.28","4800":"1.98","5000":"1.74","5200":"1.64","5400":"1.45","5600":"1.15","5800":"1.11","6000":"0.98","6200":"0.80","6400":"0.63","6600":"0.66","6800":"0.61","7000":"0.61","7200":"0.43","7400":"0.34","7600":"0.42","7800":"0.43","8000":"0.00"}';
        $nextPeriod = '{"0":"0.00","200":"0.37","400":"0.69","600":"1.40","800":"1.77","1000":"2.78","1200":"3.41","1400":"3.84","1600":"4.40","1800":"4.99","2000":"5.24","2200":"5.75","2400":"6.11","2600":"5.92","2800":"5.98","3000":"5.43","3200":"5.19","3400":"4.47","3600":"4.00","3800":"3.59","4000":"3.15","4200":"2.88","4400":"2.34","4600":"2.12","4800":"1.95","5000":"1.74","5200":"1.49","5400":"1.28","5600":"1.03","5800":"1.05","6000":"0.85","6200":"0.83","6400":"0.67","6600":"0.62","6800":"0.65","7000":"0.58","7200":"0.45","7400":"0.37","7600":"0.33","7800":"0.30","8000":"0.00"}';

        $prevPeriod = json_decode($prevPeriod, true);
        $nextPeriod = json_decode($nextPeriod, true);

        $prevPeriodKeys   = json_encode(array_keys($prevPeriod));
        $prevPeriodValues = json_encode(array_values($prevPeriod));

        $nextPeriodKeys   = json_encode(array_keys($nextPeriod));
        $nextPeriodValues = json_encode(array_values($nextPeriod));

        return $this->render('diagrams/release_compare.html.twig',
            [
                'prev_period_keys'   => $prevPeriodKeys,
                'prev_period_values' => $prevPeriodValues,
                'next_period_keys'   => $nextPeriodKeys,
                'next_period_values' => $nextPeriodValues,
            ]
        );
    }

    /**
     * @Route("/diagrams/waterfalls/list", name="diagrams_waterfalls_list")
     */
    public function waterfallsList()
    {
        $beacon = '{"https://www.":{"darvart.de/":{"holzfliegen.html":"6,29s,14c,ia,i9,dt,5q,5q,6*1obr,gj,2kz0","skin/frontend/darvart/default/images/":{"darvart-navy-logo.png":"*01y,1y,1l,2t,2s,2s|129s,2h6,2h5,4*12h5,bs","icon_sprite@2x.png":"42a5,35t,346,2gr,2gr,2,2*18m7,bv","opc-ajax-loader.gif":"42at,358,357,2g5*15sj,bt"},"media/":{"js/387cfdcd3b7e1ac11df96a1adb39c25b.js":"32a1,4ap,2gw,1*12yh1,h2,8pfd*24","catalog/product/cache/1/small_image/280x/17f82f742ffe127f42dca9de82fb58b1/d/a/darvena-papi":{"jonka-dilov-01.jpg":"*09l,7q,86,2o,9o,7s|12b4,3nr,3mk,34v*18j0,bv","onka-":{"mini-rudolf.jpg":"*09l,7q,86,b7,9o,7s|12b5,3nq,3n6,34x*161j,bu","classic-rudolf_1.jpg":"*09l,7q,86,jq,9o,7s|12b5,3yb,3y8,3j2,3j0,2ye,2ft,2ft,2ft*16ky,bv","golyam-sechko.jpg":"*09l,7q,86,s9,9o,7s|12b5,3zo,3y9,3j2,3j1,34t,34t,34t,34t*19ef,bw"}}}},"google-analytics.com/":{"analytics.js":"36md,2b,22,5*1b3m,5g,g40*25","collect?v=1&_v=j68&aip=1&a=1185933321&t=pageview&_s=1&dl=https%3A%2F%2Fwww.darvart.de%2Fholzfliegen.html&ul=en-us&de=UTF-8&dt=Handgefertigte%20Holzfliegen%20f%C3%BCr%20M%C3%A4nner%20und%20Frauen%20%7C%20DarvArt.de&sd=24-bit&sr=1440x900&vp=1391x304&je=0&_u=QACAAEAB~&jid=&gjid=&cid=1241074160.1507998957&tid=UA-89019502-1&_gid=1486960115.1528871993&z=49870228":"16p8,1s"}}}';

        $rederer = new WaterfallSvgRenderer();
        $resTimingDecompressor = new ResourceTimingDecompressor_v_0_3_4();

        /** @todo: pass the nav and res timing data to renderer and show something */
        $res = $resTimingDecompressor->decompressResources(json_decode($beacon, true));

        return $this->render('diagrams/waterfalls_list.html.twig');
    }

}
