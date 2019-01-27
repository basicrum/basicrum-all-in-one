<?php

namespace App\Controller;

use App\Entity\PageTypeConfig;
use App\Form\PageTypeConfigType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page/type/config")
 */
class PageTypeConfigController extends AbstractController
{
    /**
     * @Route("/", name="page_type_config_index", methods="GET")
     */
    public function index(): Response
    {
        $pageTypeConfigs = $this->getDoctrine()
            ->getRepository(PageTypeConfig::class)
            ->findAll();

        return $this->render('page_type_config/index.html.twig', ['page_type_configs' => $pageTypeConfigs]);
    }

    /**
     * @Route("/new", name="page_type_config_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $pageTypeConfig = new PageTypeConfig();
        $form = $this->createForm(PageTypeConfigType::class, $pageTypeConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pageTypeConfig);
            $em->flush();

            return $this->redirectToRoute('page_type_config_index');
        }

        return $this->render('page_type_config/new.html.twig', [
            'page_type_config' => $pageTypeConfig,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="page_type_config_show", methods="GET")
     */
    public function show(PageTypeConfig $pageTypeConfig): Response
    {
        return $this->render('page_type_config/show.html.twig', ['page_type_config' => $pageTypeConfig]);
    }

    /**
     * @Route("/{id}/edit", name="page_type_config_edit", methods="GET|POST")
     */
    public function edit(Request $request, PageTypeConfig $pageTypeConfig): Response
    {
        $form = $this->createForm(PageTypeConfigType::class, $pageTypeConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('page_type_config_edit', ['id' => $pageTypeConfig->getId()]);
        }

        return $this->render('page_type_config/edit.html.twig', [
            'page_type_config' => $pageTypeConfig,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="page_type_config_delete", methods="DELETE")
     */
    public function delete(Request $request, PageTypeConfig $pageTypeConfig): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pageTypeConfig->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pageTypeConfig);
            $em->flush();
        }

        return $this->redirectToRoute('page_type_config_index');
    }
}
