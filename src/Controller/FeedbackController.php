<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Feedback;

class FeedbackController extends AbstractController
{

    /**
     * @Route("/feedback/send", name="feedback_send")
     */
    public function index()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $feedback = new Feedback();
        $feedback->setMessage(json_encode($_POST));
        $feedback->setCreatedAt(new \DateTime());

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($feedback);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        $response = new Response('Thank you ^^');


        return $response;
    }

}
