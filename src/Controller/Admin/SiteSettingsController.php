<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\SiteSettings;

class SiteSettingsController extends AbstractController
{

    private $sAliases = [
        'site_email_address_from'           => 'Email Address From',
        'site_email_name_from'              => 'Name From',
        'site_email_reset_password_subject' => 'Reset Email Subject'
    ];

    /**
     * @Route("/admin/site-settings", name="admin_site_settings")
     */
    public function index()
    {
        $settings = $this->getDoctrine()
            ->getRepository(SiteSettings::class)
            ->findAll();
        return $this->render('admin/site_settings/index.html.twig', [
            'controller_name'   => 'SiteSettingsController',
            'settings'          => $settings,
            'aliases'           => $this->sAliases,
        ]);
    }

    /**
     * @Route("/admin/site-settings-save", name="admin_site_settings_save")
     */
    public function save(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        foreach($request->request->get('name') as $key => $v )
        {
            $entry = $entityManager->getRepository(SiteSettings::class)->find($key);
            if ( ! $entry )
            {
                throw $this->createNotFoundException(
                    'No entry found for id '.$id
                );
            }

            $entry->setValue($request->request->get('value')[$key]);
            $entityManager->flush();
        }
        exit();
    }
}
