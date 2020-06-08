<?php

declare(strict_types=1);

namespace App\Controller;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\DiagramSchema;
use App\BasicRum\Release;
use App\Entity\Widgets;
use App\Repository\WidgetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WidgetController extends AbstractController
{
    /**
     * @Route("/widget/generate_diagram", name="widget_generate_diagram")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    public function generateDiagram(DiagramOrchestrator $diagramOrchestrator, DiagramBuilder $diagramBuilder, Release $release)
    {
        ini_set('memory_limit', '-1');

        $diagramOrchestrator->load($_POST);

        $data = $diagramBuilder->build($diagramOrchestrator, $_POST, $release);

        return $this->json($data);
    }

    /**
     * @Route("/widget/schema/{type}", name="widgets_schema")
     */
    public function generateSchema($type)
    {
        $schema = new DiagramSchema($type);

        $response = new Response(
            json_encode(
                $schema->generateSchema(),
                JSON_PRETTY_PRINT
            )
        );

        return $response;
    }

    /**
     * @Route("/widget/main", name="widgets_index")
     */
    public function index(WidgetsRepository $widgetsRepository): Response
    {
        return $this->render('widgets/widgets.html.twig', [
            'widgets' => $widgetsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/widget/save", name="widgets_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $widget = new Widgets();

        $entityManager = $this->getDoctrine()->getManager();

        $widget->setName($request->request->get('name'))
                ->setWidget($request->request->get('widget'))
                ->setUserId($this->getUser())
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

        $errors = $validator->validate($widget);

        if (\count($errors) > 0) {
            $array['status'] = 'error';
            $i = 0;
            foreach ($errors as $key => $value) {
                $array['fields'][$i]['field'] = $value->getPropertyPath();
                $array['fields'][$i]['message'] = $value->getMessage();
                ++$i;
            }
        } else {
            $entityManager->persist($widget);
            $entityManager->flush();

            $array = [
                'status' => 'success',
                'message' => 'New Widget Created Successfully',
                'item' => [
                    'id' => $widget->getId(),
                    'name' => $widget->getName(),
                    'widget' => $widget->getWidget(),
                    'user_id' => $widget->getUserId()->getFname().' '.$widget->getUserId()->getLname(),
                    'created_at' => $widget->getCreatedAt()->format('d M Y H:i:s'),
                    'updated_at' => $widget->getUpdatedAt()->format('d M Y H:i:s'),
                ],
            ];
        }

        return $this->json($array);
    }

    /**
     * @Route("/widget/info/{id}", name="widgets_widget_info", methods={"GET"})
     */
    public function show(Widgets $widget): Response
    {
        return $this->json([
            'id' => $widget->getId(),
            'name' => $widget->getName(),
            'widget' => $widget->getWidget(),
            'user' => $widget->getUserId()->getFname().' '.$widget->getUserId()->getLname(),
            'created_at' => $widget->getCreatedAt()->format('d M Y H:i:s'),
            'updated_at' => $widget->getUpdatedAt()->format('d M Y H:i:s'),
        ]);
    }

    /**
     * @Route("/widget/update/{id}", name="widgets_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Widgets $widget, ValidatorInterface $validator): Response
    {
        $widget->setName($request->request->get('name'));
        $widget->setWidget($request->request->get('widget'));
        $widget->setUpdatedAt(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($widget);
        $entityManager->flush();

        $array = [
            'status' => 'success',
            'message' => 'Widget Updated Successfully',
            'item' => [
                'id' => $widget->getId(),
                'name' => $widget->getName(),
                'widget' => $widget->getWidget(),
                'user_id' => $widget->getUserId()->getFname().' '.$widget->getUserId()->getLname(),
                'created_at' => $widget->getCreatedAt()->format('d M Y H:i:s'),
                'updated_at' => $widget->getUpdatedAt()->format('d M Y H:i:s'),
            ],
        ];

        return $this->json($array);
    }

    /**
     * @Route("/widget/delete/{id}", name="widgets_delete")
     */
    public function delete(Request $request, Widgets $widget): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($widget);
        $entityManager->flush();

        $array = [
            'status' => 'success',
            'message' => 'Widget Deleted Successfully',
            'item' => [
                'id' => $widget->getId(),
                'name' => $widget->getName(),
                'widget' => $widget->getWidget(),
                'user_id' => $widget->getUserId()->getFname().' '.$widget->getUserId()->getLname(),
                'created_at' => $widget->getCreatedAt()->format('d M Y H:i:s'),
                'updated_at' => $widget->getUpdatedAt()->format('d M Y H:i:s'),
            ],
        ];

        return $this->json($array);
    }

    /**
     * @Route("/widget/view/{id}", name="widgets_widget_view")
     */
    public function view(Widgets $widget): Response
    {
        $array = [
            'id' => $widget->getId(),
            'name' => $widget->getName(),
            'widget' => $widget->getWidget(),
            'user' => $widget->getUserId()->getFname().' '.$widget->getUserId()->getLname(),
            'created_at' => $widget->getCreatedAt()->format('d M Y H:i:s'),
            'updated_at' => $widget->getUpdatedAt()->format('d M Y H:i:s'),
        ];

        return $this->render(
            'widgets/view.html.twig',
            [
                'item' => $array,
            ]
        );
    }
}
