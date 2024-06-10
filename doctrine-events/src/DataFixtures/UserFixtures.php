<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    const USER_REFERENCE = 'user';

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
            $user->setPassword($loginName.'password');
            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $email, $user);
        }

        $manager->flush();

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
