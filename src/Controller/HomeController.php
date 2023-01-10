<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Form\TrickEditForm;
use App\Form\TrickCreateForm;
use App\Services\TrickService;
use App\Form\CommentCreateForm;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private TrickService $trickService;

    public function __construct(TrickService $trickService, private ManagerRegistry $doctrine) {
        $this->trickService = $trickService;
    }

    #[Route('/', name: 'home')]
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
                ->setCreatedAt(new \DateTime());
                
           $entityManager->persist($trick);
           $entityManager->flush();
  
           return $this->redirectToRoute('edit',array('id' => $trick->getId()));
        }

        return $this->render('create.html.twig', [
            'TrickForm' => $form->createView(),
        ]);
    }

    #[Route('/trick/{id}', name: 'show')]
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

    #[Route('/trick/edit/{id}', name: 'edit')]
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

    #[Route('/supprime/image/{id}', name: 'annonces_delete_image', methods: "DELETE")]
    public function deleteImage(Trick $trick, Request $request, EntityManagerInterface $entityManager){
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$trick->getId(), $data['_token'])){
            $nom = $trick->getImage();
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
            //$trick->setImage('http://placehold.it/350x150');      
            //$entityManager->persist($trick);
            //$entityManager->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    #[Route('/load/trick/{limit}', name: 'load_tricks')]
    public function loadTricks($limit, TrickRepository $repo){
        $tricks = $repo->getTricks($limit);
        $htmlToRender = $this->renderView('tricks/list.html.twig', array(
            'tricks' => $tricks
        ));
        
        return new Response($htmlToRender);
    }

    #[Route('/trick/{id}/comments/{limit}', name: 'load_comments')]
    public function loadComments($id, $limit, CommentRepository $repo)
    {
        $comments = $repo->getComments($id, $limit);
        $htmlToRender = $this->renderView('comments/list.html.twig', array(
            'comments' => $comments
        ));
        
        return new Response($htmlToRender);
    }

    #[Route('/fileupload/{id}', name: 'fileupload')]
    public function fileUpload(Trick $trick, Request $request, EntityManagerInterface $entityManager){
        $file = $request->files->get('doc');
        $filename = md5(uniqid()).'.'.$file->guessExtension();

        $request->files->get('doc')->move(
            $this->getParameter('images_directory'),
            $filename
        );
     
        $image = new Image();
        $image->setUrl($filename)
            ->setTrick($trick)
            ->setCreatedAt(new \DateTime());

        $entityManager->persist($image);
        $entityManager->flush();

        $htmlToRender = $this->renderView('media/image_list.html.twig', array(
            'image' => $image
        ));

        return new Response($htmlToRender);
    }

    #[Route('/videoupload/{id}', name: 'videoupload')]
    public function videoUpload(Trick $trick, Request $request, EntityManagerInterface $entityManager)
    {
        $video = new Video();
        $video->setUrl($request->get('url'))
            ->setTrick($trick)
            ->setCreatedAt(new \DateTime());

        $entityManager->persist($video);
        $entityManager->flush();

        $htmlToRender = $this->renderView('media/video_list.html.twig', array(
            'video' => $video
        ));

        return new Response($htmlToRender);
    }
}
