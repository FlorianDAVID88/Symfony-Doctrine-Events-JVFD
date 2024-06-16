<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeInfoUserType;
use App\Form\ChangePasswordUserType;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    public function profile(Security $security): Response
    {
        $user = $security->getUser();
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);
        return $this->render('user-profile.html.twig');
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            return $security->login($user, UserAuthenticator::class, 'main');
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/profile/modify', name: 'modify_profile')]
    public function modifyProfile(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $security->getUser();
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $currentUser);
        $form = $this->createForm(ChangeInfoUserType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $currentUser instanceof User) {
            $entityManager->persist($currentUser);
            $entityManager->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/change-profile.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route(path: '/profile/change-passwd', name: 'change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                                   Security $security, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $security->getUser();
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $currentUser);
        $form = $this->createForm(ChangePasswordUserType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $currentUser instanceof User) {
            $currentUser->setPassword(
                $userPasswordHasher->hashPassword(
                    $currentUser,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($currentUser);
            $entityManager->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/change-password.html.twig', [
            'form' => $form,
        ]);
    }
}