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

        return $this->render('page_type_config/index.html.twig',
            [
                'page_types' => $pageTypes,
                'condition_options' => $conditionOptions
            ]
        );
    }

    /**
     * @Route("/page/type/config", name="page_type_config")
     */
    public function index()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $pageTypeConfig = new PageTypeConfig();
        $pageTypeConfig->setPageTypeName('Category');
        $pageTypeConfig->setConditionsSerialized(33332);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($pageTypeConfig);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new Page Type with id '. $pageTypeConfig->getId());
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

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}
