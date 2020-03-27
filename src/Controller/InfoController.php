<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/info/about", name="info_about")
     */
    public function about()
    {
        return $this->render('info/about.html.twig');
    }

    /**
     * @Route("/info/next", name="info_next")
     */
    public function next()
    {
        return $this->render('info/next.html.twig');
    }
}
