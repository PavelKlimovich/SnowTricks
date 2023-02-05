<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\ImageTypeForm;
use App\Form\TrickTypeForm;
use App\Form\VideoTypeForm;
use App\Form\CommentTypeForm;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Services\HelperStringService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index')]
    public function index(TrickRepository $repo): Response
    {
        $tricks = $repo->getFirstTricks();

        return $this->render('index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/trick/show/{slug}', name: 'show', requirements: ['slug' => '[a-zA-Z0-9\-_\/]+'])]
    public function show(Request $request, Trick $trick, CommentRepository $repo, Security $security): Response
    {
        $newComment = new Comment();
        $form = $this->createForm(CommentTypeForm::class, $newComment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setUser($security->getUser())
                ->setTrick($trick)
                ->setCreatedAt(new \DateTime());

            $this->entityManager->persist($newComment);
            $this->entityManager->flush();
        }

        $comments = $repo->getFirstComments($trick->getId());

        return $this->render('show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'CommentTypeForm' => $form->createView(),
        ]);
    }

    #[Route('/trick/create', name: 'create')]
    public function create(Request $request, Security $security): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickTypeForm::class, $trick);
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
                
           $this->entityManager->persist($trick);
           $this->entityManager->flush();
           $this->addFlash('success', 'Trick Created !');

           return $this->redirectToRoute('edit',array('slug' => $trick->getSlug()));
        }

        return $this->render('create.html.twig', [
            'TrickForm' => $form->createView(),
        ]);
    }


    
    #[Route('/trick/edit/{slug}', name: 'edit', requirements: ['slug' => '[a-zA-Z0-9\-_\/]+'])]
    public function edit(Request $request, Trick $trick, Security $security): Response
    {
        $form = $this->createForm(TrickTypeForm::class, $trick);
        $imageForm = $this->createForm(ImageTypeForm::class, null, [
            'action' => $this->generateUrl('image_update'),
            'method' => 'POST',
        ]);
        $videoForm = $this->createForm(VideoTypeForm::class, null, [
            'action' => $this->generateUrl('video_update'),
            'method' => 'POST',
        ]);
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
                
           $this->entityManager->persist($trick);
           $this->entityManager->flush();
           $this->addFlash('success', 'Trick Modified !');
        }

        return $this->render('edit.html.twig', [
            'trick' => $trick,
            'TrickTypeForm' => $form->createView(),
            'ImageTypeForm' => $imageForm->createView(),
            'VideoTypeForm' => $videoForm->createView(),
        ]);
    }

    #[Route('/delete/trick/{id}', name: 'delete')]
    public function deleteImage(Trick $trick)
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
        $this->addFlash('success', 'Trick Deleted !');

        return $this->redirectToRoute('index');
    }

    #[Route('/load/trick/{limit}', name: 'load_tricks')]
    public function loadTricks($limit, TrickRepository $repo)
    {
        $tricks = $repo->getTricks($limit);
        $htmlToRender = $this->renderView('tricks/list.html.twig', array(
            'tricks' => $tricks
        ));
        
        return new Response($htmlToRender);
    }
}
