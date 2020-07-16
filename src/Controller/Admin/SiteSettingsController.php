<?php

namespace App\Controller\Admin;

use App\Entity\SiteSettings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteSettingsController extends AbstractController
{
    private $settingsAliases = [
        'site_email_address_from' => [
            'validation_rules' => [
                'type' => 'email',
                'minlength' => 3,
                'required' => '',
            ],
            'alias' => 'Email Address From',
        ],
        'site_email_name_from' => [
            'validation_rules' => [
                'type' => 'text',
                'minlength' => '3',
                'required' => '',
            ],
            'alias' => 'Name From',
        ],
        'site_email_reset_password_subject' => [
            'validation_rules' => [
                'type' => 'text',
                'minlength' => 3,
                'required' => '',
            ],
            'alias' => 'Reset Password Email Subject',
        ],
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
            'settings' => $settings,
            'aliases' => $this->settingsAliases,
        ]);
    }

    /**
     * @Route("/admin/site-settings-save", name="admin_site_settings_save")
     */
    public function save(Request $request)
    {
        $response = [
            'status' => 'success',
            'message' => 'Saved Successfully',
        ];

        $entityManager = $this->getDoctrine()->getManager();
        foreach ($request->request->get('name') as $key => $v) {
            $entry = $entityManager->getRepository(SiteSettings::class)->find($key);
            if (!$entry) {
                $response = [
                    'status' => 'error',
                    'message' => $v.' No such entry found',
                ];
            }

            $entry->setValue($request->request->get('value')[$key]);
            $entityManager->flush();
        }

        return $this->json($response);
    }
}
