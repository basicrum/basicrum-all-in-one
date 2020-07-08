<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin/register", name="admin_register_form")
     */
    public function form()
    {
        return $this->render('admin/register_form.html.twig');
    }

    /**
     * @Route("/admin/register/save", name="admin_register_form_save")
     */
    public function formSave(Request $request, ValidatorInterface $validator)
    {
        $user = new User();

        $entityManager = $this->getDoctrine()->getManager();

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $request->request->get('password'));

        $user->setFname($request->request->get('fname'))
             ->setLname($request->request->get('lname'))
             ->setPlainPassword($request->request->get('password'))
             ->setRepeatPlainPassword($request->request->get('repeat_password'))
             ->setEmail($request->request->get('email'))
             ->setPassword($encodedPassword)
             ->setRoles([$request->request->get('user_role')]);

        $errors = $validator->validate($user);

        if (\count($errors) > 0) {
            $result['status'] = 'error';
            $i = 0;
            foreach ($errors as $key => $value) {
                $result['fields'][$i]['field'] = $value->getPropertyPath();
                $result['fields'][$i]['message'] = $value->getMessage();
                ++$i;
            }
        } else {
            $entityManager->persist($user);
            $entityManager->flush();

            $result = [
                'status' => 'success',
                'message' => 'New User Created Successfully',
                'user' => [
                    'id' => $user->getId(),
                    'fname' => $user->getFname(),
                    'lname' => $user->getLname(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ],
            ];
        }

        return $this->json($result);
    }
}
