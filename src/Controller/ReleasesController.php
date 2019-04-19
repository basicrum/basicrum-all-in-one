<?php

namespace App\Controller;

use App\Entity\Releases;
use App\Form\ReleasesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/releases")
 */
class ReleasesController extends AbstractController
{
    /**
     * @Route("/", name="releases_index", methods="GET")
     */
    public function index(): Response
    {
        $releases = $this->getDoctrine()
            ->getRepository(Releases::class)
            ->findAll();

        return $this->render('releases/index.html.twig', ['releases' => $releases]);
    }

    /**
     * @Route("/new", name="releases_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $release = new Releases();
        $form = $this->createForm(ReleasesType::class, $release, [
            'action' => $this->generateUrl('releases_new'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($release);
            $em->flush();

            return $this->redirect('/index#/releases/');
        }

        return $this->render('releases/new.html.twig', [
            'release' => $release,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="releases_show", methods="GET")
     */
    public function show(Releases $release): Response
    {
        return $this->render('releases/show.html.twig', ['release' => $release]);
    }

    /**
     * @Route("/{id}/edit", name="releases_edit", methods="GET|POST")
     */
    public function edit(Request $request, Releases $release): Response
    {
        $form = $this->createForm(ReleasesType::class, $release, [
            'action' => $this->generateUrl('releases_edit', ['id' => $release->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect('/index#/releases/');
        }

        return $this->render('releases/edit.html.twig', [
            'release' => $release,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="releases_delete", methods="DELETE")
     */
    public function delete(Request $request, Releases $release): Response
    {
        if ($this->isCsrfTokenValid('delete'.$release->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($release);
            $em->flush();
        }

        return $this->redirect('/index#/releases/');
    }
}
