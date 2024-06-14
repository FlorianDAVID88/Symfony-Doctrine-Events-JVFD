<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Place;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function app(EventRepository $repository): Response
    {
        return $this->render('base.html.twig', [
            'events' => $repository->findAllOrderedByDate()
        ]);
    }

    #[Route('/event-{id}', name: 'show_event')]
    public function show(int $id, EventRepository $repository): Response {
        $event = $repository->find($id);
        if (!$event) {
            throw $this->createNotFoundException(
                'Aucun événement référencé avec l\'id '.$id
            );
        }

        return $this->render('event/show_event.html.twig', [
            'event' => $event
        ]);
    }

    #[Route('/event/new', name: 'event_new')]
    public function new(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/new-event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event/create', name: 'event_create')]
    public function create(Request $request, ManagerRegistry $managerRegistry, Security $security): Response {
        $entityManager = $managerRegistry->getManager();

        // Récupération des données du formulaire
        $formData = $request->request->all()['event_form'];
        $event = new Event();
        $event->setTitle($formData['title']);
        $event->setDescription($formData['description']);
        $event->setDatetime(new \DateTimeImmutable($formData['datetime']));
        $event->setNbMaxUsers($formData['nb_max_users']);
        $event->setPublic(isset($formData['is_public']));
        $event->setCreator($security->getUser());
        $event->setPlace($entityManager->getReference(Place::class, $formData['place']));

        $entityManager->persist($event);
        $entityManager->flush();
        return $this->redirectToRoute('app_home');
    }

    #[Route('/event/list', name: 'list_all_events')]
    public function listAllEvents(EventRepository $repository): Response
    {
        return $this->render('event/list-events-search.html.twig', [
            'events' => $repository->findAll()
        ]);
    }
}
