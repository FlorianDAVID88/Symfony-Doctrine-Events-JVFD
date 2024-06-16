<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Security\Voter\EventVoter;
use App\Service\EventService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;

class EventController extends AbstractController
{
    private $eventService;
    private $mailerService;

    public function __construct(EventService $eventService, MailerService $mailerService)
    {
        $this->eventService = $eventService;
        $this->mailerService = $mailerService;
    }

    #[Route('/', name: 'app_home')]
    public function app(EventRepository $repository, Security $security): Response
    {
        $userExists = $security->getUser() instanceof User;
        $events = $repository->findNextEventsByDate(!$userExists);
        return $this->render('base.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/event-{id}', name: 'show_event')]
    public function show(int $id, EventRepository $repository): Response {
        $event = $repository->find($id);
        $this->denyAccessUnlessGranted(EventVoter::VIEW, $event);

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
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $event = new Event();
        $this->denyAccessUnlessGranted(EventVoter::ADD, $event);

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $event->setCreator($user);
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/new-event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event/list', name: 'list_events')]
    public function listAllEvents(Request $request, EventRepository $repository): Response
    {
        $queries = $request->query;

        return $this->render('event/list-events-search.html.twig', [
            'events' => $repository->getFromFilters($queries->get('name'), $queries->get('date_min'),
                                                    $queries->get('date_max'), $queries->get('public'),
                                                    $queries->get('private')),
            'filters' => $queries,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/event-{id}/add-user', name: 'add_user_to_event')]
    public function addUserToEvent(int $id, EventRepository $repository,
                                   ManagerRegistry $managerRegistry, Security $security): Response
    {
        $entityManager = $managerRegistry->getManager();
        $event = $repository->find($id);
        $this->denyAccessUnlessGranted(EventVoter::ADD, $event);

        $nb_places_dispo = $this->eventService->calculateAvailablePlaces($event);

        if ($nb_places_dispo > 0) {
            $currentUser = $security->getUser();

            if (!$event) {
                throw $this->createNotFoundException(
                    'Aucun événement référencé avec l\'id '.$id
                );
            } else if ($currentUser instanceof User) {
                $event->addInscrit($currentUser);
                $entityManager->persist($event);
                $entityManager->flush();
                $this->mailerService->sendEmailForInscription($currentUser, $event);
            }
        }

        return $this->redirectToRoute('show_event', ['id' => $id]);
    }

    #[Route('/event-{id}/update', name: 'event_edit')]
    public function editEvent(int $id, Request $request, EventRepository $repository,
                              EntityManagerInterface $entityManager): Response
    {
        $event = $repository->find($id);
        $this->denyAccessUnlessGranted(EventVoter::EDIT, $event);

        if (!$event) {
            throw $this->createNotFoundException('Aucun événement trouvé pour l\'id '.$id);
        }

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/update-event.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/event-{id}/delete', name: 'event_delete')]
    public function deleteEvent(int $id, EventRepository $repository, ManagerRegistry $managerRegistry, Security $security): Response
    {
        $entityManager = $managerRegistry->getManager();
        $event = $repository->find($id);
        $this->denyAccessUnlessGranted(EventVoter::DELETE, $event);

        if (!$event) {
            throw $this->createNotFoundException('Aucun événement trouvé pour l\'id '.$id);
        }

        $entityManager->remove($event);
        $entityManager->flush();
        $this->mailerService->sendEmailForAnnulation($security->getUser(), $event);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/event-{id}/desinscription', name: 'desinscription_to_event')]
    public function desinscriptionToEvent(int $id, EventRepository $repository,
                                          ManagerRegistry $managerRegistry, Security $security): Response
    {
        $entityManager = $managerRegistry->getManager();
        $event = $repository->find($id);
        $this->denyAccessUnlessGranted(EventVoter::USER_DESINSCRIPTION, $event);

        $currentUser = $security->getUser();

        if(!$event) {
            throw $this->createNotFoundException('Aucun événement référencé avec l\'id '.$id);
        } else if ($currentUser instanceof User) {
            if($currentUser->getEventsInsc()->contains($event)) {
                $event->removeInscrit($currentUser);
                $entityManager->persist($event);
                $entityManager->flush();
            }

            return $this->redirectToRoute('show_event', ['id' => $id]);
        }
    }
}
