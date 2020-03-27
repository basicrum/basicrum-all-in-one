<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\WidgetsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Widgets;

/**
 * @Route("/widgets")
 */
class WidgetsController extends AbstractController
{
    /**
     * @Route("/main", name="widgets_index")
     */
    public function index(WidgetsRepository $widgetsRepository): Response
    {
        return $this->render('widgets/widgets.html.twig', [
            'widgets' => $widgetsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="widgets_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $widget = new Widgets();
        $form = $this->createForm(WidgetsType::class, $widget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($widget);
            $entityManager->flush();

            return $this->redirectToRoute('widgets_index');
        }

        return $this->render('widgets/new.html.twig', [
            'widget' => $widget,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="widgets_show", methods={"GET"})
     */
    public function show(Widgets $widget): Response
    {
        return $this->render('widgets/show.html.twig', [
            'widget' => $widget,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="widgets_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Widgets $widget): Response
    {
        $form = $this->createForm(WidgetsType::class, $widget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('widgets_index');
        }

        return $this->render('widgets/edit.html.twig', [
            'widget' => $widget,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="widgets_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Widgets $widget): Response
    {
        if ($this->isCsrfTokenValid('delete'.$widget->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($widget);
            $entityManager->flush();
        }

        return $this->redirectToRoute('widgets_index');
    }
}
