<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/fileupload/{id}', name: 'fileupload')]
    public function fileUpload(Trick $trick, Request $request)
    {
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

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $htmlToRender = $this->renderView('media/image_list.html.twig', array(
            'image' => $image
        ));

        return new Response($htmlToRender);
    }

    #[Route('/image_delete/{image}', name: 'image_delete')]
    public function imageDelete(Image $image, Request $request)
    {
        $this->entityManager->remove($image);
        $this->entityManager->flush();
        $route = $request->headers->get('referer');
        $this->addFlash('success', 'Image Deleted !');

        return $this->redirect($route);
    }

    #[Route('/image_update', name: 'image_update')]
    public function imageUpdate(Request $request)
    {
        $form = $request->get('image_edit_form');
        $imageRepository = $this->entityManager->getRepository(Image::class);
        $trickRepository = $this->entityManager->getRepository(Trick::class);
        $this->entityManager->remove($imageRepository->find($form['hidden']));

        $file = $request->files->get('image_edit_form')['image'];
        $filename = md5(uniqid()).'.'.$file->guessExtension();

        $request->files->get('image_edit_form')['image']->move(
            $this->getParameter('images_directory'),
            $filename
        );
     
        $image = new Image();
        $image->setUrl($filename)
            ->setTrick($trickRepository->find($form['trick_id']))
            ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $route = $request->headers->get('referer');
        $this->addFlash('success', 'Image Update !');

        return $this->redirect($route);
    }
}
