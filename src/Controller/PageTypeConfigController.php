<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\PageTypeConfig;

class PageTypeConfigController extends Controller
{

    /**
     * @Route("/page/type/config/list", name="page_type_config_list")
     */
    public function typesList()
    {
        $conditionOptions = [
            'regex'    => 'RegEx',
            'contains' => 'Contains',
            'endson'   => 'Ends On'
        ];

        $pageTypes = $this->getDoctrine()
            ->getRepository(PageTypeConfig::class)
            ->findAll();

        $pageTypesArr = [];

        foreach ($pageTypes as $pageType) {
            $pageTypesArr[] = [
                'id'        => $pageType->getId(),
                'name'      => $pageType->getPageTypeName(),
                'condition' => json_decode($pageType->getConditionsSerialized(), true)
            ];
        }

        return $this->render('page_type_config/index.html.twig',
            [
                'page_types'        => $pageTypesArr,
                'condition_options' => $conditionOptions
            ]
        );
    }

    /**
     * @Route("/page/type/config/{id}", name="page_type_config_show")
     */
    public function show($id)
    {
        $pageTypeConfig = $this->getDoctrine()
            ->getRepository(PageTypeConfig::class)
            ->find($id);

        if (!$pageTypeConfig) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return new Response('Check out this great product: '.$pageTypeConfig->getPageTypeName());
    }


    /**
     * @Route("/page/type/save", name="page_type_config_save")
     */
    public function save()
    {
        $data = $_POST;

        $pageTypeConfig = $this->getDoctrine()
            ->getRepository(PageTypeConfig::class)
            ->find($data['page_type_id']);

        if ($pageTypeConfig) {
            // you can fetch the EntityManager via $this->getDoctrine()
            // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
            $entityManager = $this->getDoctrine()->getManager();

            $pageTypeConfig->setPageTypeName($data['page_type_name']);
            $pageTypeConfig->setConditionsSerialized(
                json_encode([
                    'page_type_rule_condition' => $data['page_type_rule_condition'],
                    'page_type_rule_value'     => $data['page_type_rule_value']

                ])
            );

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($pageTypeConfig);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return new Response('Saved');
        }

        return new Response('Not updated');
    }
}
