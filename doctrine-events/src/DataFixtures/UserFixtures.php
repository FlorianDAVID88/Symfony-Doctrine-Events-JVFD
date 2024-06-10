<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    const USER_REFERENCE = 'user';
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['firstname' => 'Jean', 'lastname' => 'DUPONT'],
            ['firstname' => 'Martin', 'lastname' => 'LAFORET'],
            ['firstname' => 'Patricia', 'lastname' => 'DUPONT'],
            ['firstname' => 'Sandra', 'lastname' => 'LAFORET']
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);

            $loginName = strtolower($data['firstname'] . '.' . $data['lastname']);
            $email = strtolower($loginName) . '@email.fr';
            $user->setEmail($email);

            $password = $this->passwordHasher->hashPassword($user, $loginName . 'password');
            $user->setPassword($password);

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $email, $user);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
