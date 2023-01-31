<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/trick/{id}/comments/{limit}', name: 'load_comments')]
    public function loadComments($id, $limit, CommentRepository $repo)
    {
        $comments = $repo->getComments($id, $limit);
        $htmlToRender = $this->renderView('comments/list.html.twig', array(
            'comments' => $comments
        ));
        
        return new Response($htmlToRender);
    }

}
