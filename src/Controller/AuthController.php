<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
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
}
