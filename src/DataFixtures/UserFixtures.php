<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Admin');
        $user->setUsername('admin');
        $password = $this->encoder->encodePassword($user, 'adminpassword');
        $user->setPassword($password);
        $user->setRoles('ADMIN');
        $manager->persist($user);

        $manager->flush();
    }
}
