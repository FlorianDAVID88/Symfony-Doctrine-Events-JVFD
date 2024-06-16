<?php

namespace App\Service;

use App\Entity\Event;

class EventService
{
    public function calculateAvailablePlaces(Event $event): int
    {
        $totalPlaces = $event->getNbMaxUsers();
        $registeredUsers = $event->getInscrits()->count();
        return $totalPlaces - $registeredUsers;
    }
}
