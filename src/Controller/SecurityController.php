<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/registration', name: 'registration')]
    public function registration(): Response
    {
        return $this->render('auth/registration.html.twig');
    }

    #[Route('/forgot_password', name: 'forgot_password')]
    public function forgotPassword(): Response
    {
        return $this->render('auth/forgot_password.html.twig');
    }

    #[Route('/reset_password', name: 'reset_password')]
    public function resetPassword(): Response
    {
        return $this->render('auth/reset_password.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('home');
    }
}
