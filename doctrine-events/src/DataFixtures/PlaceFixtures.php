<?php

namespace App\DataFixtures;

use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceFixtures extends Fixture implements OrderedFixtureInterface
{
    const PLACE_REFERENCE = 'place';

    public function load(ObjectManager $manager): void
    {
        $places = [
            ['name' => 'Centre des Congrès - Espace L.A.C.', 'address' => '17 Faubourg de Ramberchamp, 88400 Gérardmer', 'longitude' => '48.0665552', 'latitude' => '6.8609508'],
            ['name' => 'Boulevard Kelsch', 'address' => 'Boulevard Kelsch, 88400 Gérardmer', 'longitude' => '48.0732626', 'latitude' => '6.87823'],
            ['name' => 'Rue François Mitterrand', 'address' => '5 Rue François Mitterrand, 88400 Gérardmer', 'longitude' => '48.071325', 'latitude' => '6.872900'],
            ['name' => 'Casino JOA', 'address' => '3 Avenue de la Ville de Vichy, 88400 Gérardmer', 'longitude' => '48.0694185', 'latitude' => '6.8669045'],
            ['name' => 'Lac de Gérardmer', 'address' => 'Quai de Waremme, 88400 Gérardmer', 'longitude' => '48.071476', 'latitude' => '6.864927'],
        ];

        foreach ($places as $data) {
            $place = new Place();
            $place->setName($data['name']);
            $place->setAddress($data['address']);
            $place->setLongitude($data['longitude']);
            $place->setLatitude($data['latitude']);
            $manager->persist($place);
            $this->addReference(self::PLACE_REFERENCE . $data['name'], $place);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
