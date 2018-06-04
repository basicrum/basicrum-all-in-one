<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/ajax/dashboard", name="page_dashboard")
     */
    public function index()
    {
        return $this->render('dashboard.html.twig');
    }

}
