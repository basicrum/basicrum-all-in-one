<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\User;

class UserLoginController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/forgot-password-form", name="app_forgot-password-form")
     */
    public function forgotPasswordForm(Request $request, \Swift_Mailer $mailer)
    {
        $error['messageData'] = '';
        $lastUsername = '';

        if ( $request->request->has('email') )
        {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $request->request->get('email')]);

            if ( $user )
            {
                // find one
                // restore_password
                $string = $user->getId().$user->getEmail().microtime();
                $stringHash = sha1($string);
                $user->setRestorePassword($stringHash);

                $entityManager->flush();

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('admin@tux.lc')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/forgot_password_initial.html.twig',
                            ['stringHash' => base64_encode($stringHash)]
                        ),
                        'text/html'
                    )

                    // you can remove the following code if you don't define a text version for your emails
                    /*->addPart(
                        $this->renderView(
                            // templates/emails/registration.txt.twig
                            'emails/registration.txt.twig',
                            ['name' => $name]
                        ),
                        'text/plain'
                    )*/
                ;

                $mailer->send($message);


                $error['messageData']   = "An email sent!";
                $lastUsername           = $request->request->get('email');
            }
            else
            {
                $error['messageData']   = "Email not found";
                $lastUsername           = $request->request->get('email');
            }
        }


        return $this->render('security/forgot_password_form.html.twig', [
            'error'         => $error,
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * @Route("/forgot-password-reset/{hash}", name="app_forgot-password-reset")
     */
    public function forgotPasswordReset($hash)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['restore_password' => base64_decode($hash)]);

        if ( $user )
        {
            return $this->redirect($this->generateUrl('app_forgot-password-new-password', ['hash' => $hash]));// redirect to form
        }

        return $this->render('security/forgot_password_fail.html.twig');
    }

    /**
     * @Route("/forgot-password-new-password/{hash}", name="app_forgot-password-new-password")
     */
    public function forgotPasswordNewPassword($hash, Request $request, ValidatorInterface $validator)
    {
        $error = '';

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['restore_password' => base64_decode($hash)]);

        if ( $user )
        {
            if ( $request->request->has('password') )
            {
                $encodedPassword = $this->passwordEncoder->encodePassword($user, $request->request->get('password'));

                $user->setPlainPassword($request->request->get('password'))
                    ->setRepeatPlainPassword($request->request->get('repeat_password'))
                    ->setPassword($encodedPassword);

                $errors = $validator->validate($user);

                if (count($errors) > 0)
                {
                    foreach ($errors as $key => $value)
                    {
                        // echo $value->getPropertyPath(); exit();
                        $error = $value->getMessage();
                    }
                }
                else
                {
                    $user->setRestorePassword(null);
                    $entityManager->flush();
                    return $this->redirect('/login');
                }
            }

            return $this->render('security/reset_password_form.html.twig', [
                'error'         => $error
            ]);
        }
        else
        {
            return $this->render('security/forgot_password_fail.html.twig');
        }
    }
}
