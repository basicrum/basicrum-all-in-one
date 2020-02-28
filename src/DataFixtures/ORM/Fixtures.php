<?php

namespace App\DataFixtures\ORM;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class Fixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
	$user = new User();

	$password = $this->encoder->encodePassword($user, 'password');

	$user
	    ->setEmail('admin@admin.lc')
	    ->setPassword($password)
	    ->setFname('User')
	    ->setLname('The Admin')
	    ->setRoles(['ROLE_ADMIN']);
	
	$manager->persist($user);
	$manager->flush();
    }
}
