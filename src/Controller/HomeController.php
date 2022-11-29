<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\EditType;
use App\Entity\Comment;
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
    public function show(Trick $trick): Response
    {
        $comments = $trick->getComments();

        //foreach ($comments as $comment) {
        //    dump($comment);
        //}
        //die;
        return $this->render('show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
        ]);
    }

    #[Route('/trick/edit/{id}', name: 'edit')]
    public function edit(Request $request,TrickRepository $repo, $id, EntityManagerInterface $entityManager, Security $security): Response
    {
        $trick = $repo->find($id);
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
            
            $trick->setName($form->get('name')->getData())
                ->setImage($fichier)
                ->setContent($form->get('content')->getData())
                ->setCategory($form->get('category')->getData())
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
}
