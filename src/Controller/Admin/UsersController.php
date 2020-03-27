<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function form()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $roles = ['ROLE_USER' => 'User', 'ROLE_ADMIN' => 'Admin'];

        return $this->render('admin/users.html.twig', ['users' => $users, 'allRoles' => $roles]);
    }

    /**
     * @Route("/admin/user-edit-profile", name="admin_edit_profile")
     */
    public function profileForm()
    {
        return $this->render('admin/profile.html.twig');
    }

    /**
     * @Route("/admin/user/info/{id}", name="admin_user_info")
     */
    public function getUserInfo(User $user)
    {
        $array = [
            'fname' => $user->getFname(),
            'lname' => $user->getLname(),
            'email' => $user->getEmail(),
            'role' => $user->getRoles(),
        ];

        echo json_encode($array);
        exit();

        return new JsonResponse($array);
    }

    /**
     * @Route("/admin/user/update/{id}", name="admin_user_update")
     */
    public function update($id, Request $request, ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $user->setFname($request->request->get('fname'))
             ->setLname($request->request->get('lname'))
             ->setEmail($request->request->get('email'));

        if ($request->request->get('user_role')) {
            $user->setRoles([$request->request->get('user_role')]);
        }

        if ($request->request->get('password')) {
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $request->request->get('password'));

            $user->setPlainPassword($request->request->get('password'))
                ->setRepeatPlainPassword($request->request->get('repeat_password'))
                ->setPassword($encodedPassword);
        }

        $entityManager->flush();

        $array = [
            'status' => 'success',
            'message' => 'User Updated Successfully',
            'user' => [
                'id' => $user->getId(),
                'fname' => $user->getFname(),
                'lname' => $user->getLname(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ];

        return new Response(json_encode($array));
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete")
     */
    public function deleteUser($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);

        $entityManager->flush();

        $array = [
            'status' => 'success',
            'message' => 'User Deleted Successfully',
            'user' => [
                'id' => $user->getId(),
                'fname' => $user->getFname(),
                'lname' => $user->getLname(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ];

        return new Response(json_encode($array));
    }
}
