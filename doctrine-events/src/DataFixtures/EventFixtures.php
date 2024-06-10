<?php

namespace App\DataFixtures;

use App\Entity\Event;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $events = [
            ['title' => 'Élection de la Reine des Jonquilles', 'description' => 'Soirée traditionnelle et fête incontournable à Gérardmer, l’élection de la Reine des Jonquilles est accompagnée d’un grand bal, le tout animé par l’excellent Jérôme Prodhomme, le réputé animateur, de France Bleu.', 'datetime' => '2024-12-19 20:00:00', 'nb_max_users' => 100, 'is_public' => false, 'place' => 'Centre des Congrès - Espace L.A.C.', 'userEmail' => 'jean.dupont@email.fr'],
            ['title' => 'Corso fleuri de la fête des Jonquilles', 'description' => "Défilé traidtionnel duquel défilent 20 à 30 chars décorés de jonquilles et d'éléments naturels issus des forêts de la région", 'datetime' => '2025-04-13 14:00:00', 'nb_max_users' => 1000, 'is_public' => true, 'place' => 'Boulevard Kelsch', 'userEmail' => 'sandra.laforet@email.fr'],
            ['title' => 'Atelier de conception de mini-statuettes', 'description' => 'Atelier de conception de mini-statuettes avec des jonquilles.', 'datetime' => '2024-09-22 09:30:00', 'nb_max_users' => 25, 'is_public' => true, 'place' => 'Rue François Mitterrand', 'userEmail' => 'jean.dupont@email.fr'],
            ['title' => 'Course cycliste', 'description' => 'Course partant du centre-ville pour parcourir les cols situés autour de la ville, du Tholy et de la Bresse. Arrivée tracée à la Mauselaine après une dernière montée de 2km à 10% de moyenne.', 'datetime' => '2024-07-13 09:00:00', 'nb_max_users' => 200, 'is_public' => true, 'place' => 'Boulevard Kelsch', 'userEmail' => 'martin.laforet@email.fr'],
            ['title' => 'Concours de belote', 'description' => '', 'datetime' => '2024-07-28 11:00:00', 'nb_max_users' => 30, 'is_public' => false, 'place' => 'Casino JOA', 'userEmail' => 'sandra.laforet@email.fr']
        ];

        foreach ($events as $data) {
            $event = new Event();
            $event->setTitle($data['title']);
            $event->setDescription($data['description']);
            $event->setDatetime(new DateTimeImmutable($data['datetime']));
            $event->setNbMaxUsers($data['nb_max_users']);
            $event->setPublic($data['is_public']);
            $event->setPlace($this->getReference(PlaceFixtures::PLACE_REFERENCE . $data['place']));
            $event->setCreator($this->getReference(UserFixtures::USER_REFERENCE . $data['userEmail']));
            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 3;
    }
}
