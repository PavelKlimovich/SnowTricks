<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickEditForm;
use App\Form\TrickCreateForm;
use App\Services\TrickService;
use App\Form\CommentCreateForm;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Services\HelperStringService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    private TrickService $trickService;

    public function __construct(TrickService $trickService, private ManagerRegistry $doctrine) 
    {
        $this->trickService = $trickService;
    }

    #[Route('/', name: 'index')]
    public function index(TrickRepository $repo): Response
    {
        $tricks = $repo->getFirstTricks();

        return $this->render('index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/trick/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickCreateForm::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            $image = $form->get('image')->getData();

            if (isset($image)) {
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move($this->getParameter('images_directory'), $fichier);
            }else{
                $fichier = $trick->getImage();
            } 
            
            $trick->setImage($fichier)
                ->setUser($security->getUser())
                ->setSlug(HelperStringService::slugify($trick->getName()))
                ->setCreatedAt(new \DateTime());
                
           $entityManager->persist($trick);
           $entityManager->flush();
           $this->addFlash('success', 'Trick Created !');

           return $this->redirectToRoute('edit',array('slug' => $trick->getSlug()));
        }

        return $this->render('create.html.twig', [
            'TrickForm' => $form->createView(),
        ]);
    }

    #[Route('/trick/{slug}', name: 'show')]
    public function show(Request $request, Trick $trick, EntityManagerInterface $entityManager, CommentRepository $repo, Security $security): Response
    {
        $newComment = new Comment();
        $form = $this->createForm(CommentCreateForm::class, $newComment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setUser($security->getUser())
                ->setTrick($trick)
                ->setCreatedAt(new \DateTime());

            $entityManager->persist($newComment);
            $entityManager->flush();
        }

        $comments = $repo->getFirstComments($trick->getId());

        return $this->render('show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'CommentCreateForm' => $form->createView(),
        ]);
    }

    #[Route('/trick/edit/{slug}', name: 'edit')]
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, Security $security): Response
    {
        $form = $this->createForm(TrickEditForm::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $image = $form->get('image')->getData();

            if (isset($image)) {
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move($this->getParameter('images_directory'), $fichier);
            }else{
                $fichier = $trick->getImage();
            } 
            
            $trick->setImage($fichier)
                ->setUser($security->getUser())
                ->setCreatedAt(new \DateTime());
                
           $entityManager->persist($trick);
           $entityManager->flush();
        }

        return $this->render('edit.html.twig', [
            'trick' => $trick,
            'TrickEditForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/trick/{id}', name: 'delete')]
    public function deleteImage(Trick $trick, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($trick);
        $entityManager->flush();
        $this->addFlash('success', 'Trick Deleted !');

        return $this->redirectToRoute('index');
    }

    #[Route('/load/trick/{limit}', name: 'load_tricks')]
    public function loadTricks($limit, TrickRepository $repo){
        $tricks = $repo->getTricks($limit);
        $htmlToRender = $this->renderView('tricks/list.html.twig', array(
            'tricks' => $tricks
        ));
        
        return new Response($htmlToRender);
    }
}
