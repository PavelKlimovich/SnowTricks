<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\EditType;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Services\TrickService;
use App\Repository\TrickRepository;
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
    public function index(): Response
    {
        $tricks = $this->trickService->getTricks();
        return $this->render('index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/trick/{id}', name: 'show')]
    public function show(Request $request, Trick $trick, EntityManagerInterface $entityManager, Security $security): Response
    {
      
        $newComment = new Comment();
        $form = $this->createForm(CommentFormType::class, $newComment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setUser($security->getUser())
                ->setTrick($trick)
                ->setCreatedAt(new \DateTime());

            $entityManager->persist($newComment);
            $entityManager->flush();
        }

        $comments = $trick->getComments();

        return $this->render('show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'CommentFormType' => $form->createView(),
        ]);
    }

    #[Route('/trick/edit/{id}', name: 'edit')]
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, Security $security): Response
    {
        $form = $this->createForm(EditType::class, $trick);
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
            'EditType' => $form->createView(),
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

    #[Route('/load/trick', name: 'load_tricks')]
    public function loadTricks(){
        $tricks = $this->trickService->getTricks();
        $htmlToRender = $this->renderView('tricks/list.html.twig', array(
            'tricks' => $tricks
        ));
        
        return new Response($htmlToRender);
    }

    #[Route('/trick/comments/{id}', name: 'load_comments')]
    public function loadComments(Trick $trick){
        $comments = $trick->getComments();
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
}
