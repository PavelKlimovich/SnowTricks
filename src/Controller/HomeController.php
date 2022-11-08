<?php

namespace App\Controller;

use App\Services\TrickService;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private TrickService $trickService;

    public function __construct(TrickService $trickService) {
        $this->trickService = $trickService;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $tricks = $this->trickService->getTricks();

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
