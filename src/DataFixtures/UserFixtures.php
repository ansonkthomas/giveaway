<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("anson2@gmail.com");
        $user->setRoles(array("ROLE_USER"));
        $user->setPassword($this->passwordEncoder->encodePassword(
             $user,
             'password'
         ));
        //$user->setPassword("password");
        $manager->persist($user);
        $manager->flush();
    }
}
