<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateSuperadmintUserCommand extends Command
{
    protected static $defaultName = 'basicrum:superadmin:create';

    private $passwordEncoder;
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create super admin user')
            ->setHelp('This command allows you to create a super admin user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->entityManager;
        $io = new SymfonyStyle($input, $output);

        $query = $em->createQuery('SELECT u FROM App\Entity\User u WHERE u.roles LIKE \'%ROLE_ADMIN%\'');
        $exists_users = $query->getResult();

        if ($exists_users) {
            $io->error('Admin user already exist. Can not continue..');
            die();
        }

        $helper = $this->getHelper('question');

        $question['fname'] = new Question('Please enter Super User first name : ');
        $question['fname']->setMaxAttempts(20);
        $question['fname']->setValidator(function ($fname) {
            if ('' == trim($fname)) {
                throw new \RuntimeException('The first name can not be empty');
            }

            return $fname;
        });
        $answer['fname'] = $helper->ask($input, $output, $question['fname']);

        $question['lname'] = new Question('Please enter Super User first name : ', 'Last Name');
        $question['lname']->setMaxAttempts(20);
        $question['lname']->setValidator(function ($lname) {
            if ('' == trim($lname)) {
                throw new \RuntimeException('The last name can not be empty');
            }

            return $lname;
        });
        $answer['lname'] = $helper->ask($input, $output, $question['lname']);

        $question['email'] = new Question('Please enter Super User email : ');
        $question['email']->setMaxAttempts(3);
        $question['email']->setValidator(function ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Please provide real email address');
            }

            return $email;
        });
        $answer['email'] = $helper->ask($input, $output, $question['email']);

        $question['password'] = new Question('Please enter Super User password : ');
        $question['password']->setHidden(true);
        $question['password']->setMaxAttempts(20);
        $question['password']->setHiddenFallback(false);
        $question['password']->setValidator(function ($password) {
            if ('' == trim($password)) {
                throw new \RuntimeException('The password can not be empty');
            }

            if (\strlen($password) < 6) {
                throw new \RuntimeException('The password can not be less than 6 signs');
            }

            return $password;
        });
        $answer['password'] = $helper->ask($input, $output, $question['password']);

        $question['repeat_password'] = new Question('Super User password again : ');
        $question['repeat_password']->setHidden(true);
        $question['repeat_password']->setMaxAttempts(20);
        $question['repeat_password']->setHiddenFallback(false);
        $question['repeat_password']->setValidator(function ($rpassword) use ($answer) {
            if (trim(rtrim((string) $rpassword)) != $answer['password']) {
                throw new \RuntimeException('Password doesn\'t match');
            }

            return $rpassword;
        });
        $answer['repeat_password'] = $helper->ask($input, $output, $question['repeat_password']);

        $user = new User();

        $password = $this->passwordEncoder->encodePassword($user, $answer['password']);

        $user
            ->setEmail($answer['email'])
            ->setPassword($password)
            ->setFname($answer['fname'])
            ->setLname($answer['lname'])
            ->setRoles(['ROLE_ADMIN'])
        ;

        $em->persist($user);
        $em->flush();

        $io->success('Super admin user with email '.$answer['email']." has been created! \n You may now login using registred email and password!");
    }
}
