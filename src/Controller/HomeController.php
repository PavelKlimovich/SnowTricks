<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(TrickRepository $repo): Response
    {
        $tricks = $repo->findAll();

        return $this->render('index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/trick/{id}', name: 'show')]
    public function show(TrickRepository $repo, $id): Response
    {
        $trick = $repo->find($id);

        return $this->render('show.html.twig', [
            'trick' => $trick
        ]);
    }
}
