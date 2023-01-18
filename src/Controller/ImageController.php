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
    #[Route('/fileupload/{id}', name: 'fileupload')]
    public function fileUpload(Trick $trick, Request $request, EntityManagerInterface $entityManager)
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

        $entityManager->persist($image);
        $entityManager->flush();

        $htmlToRender = $this->renderView('media/image_list.html.twig', array(
            'image' => $image
        ));

        return new Response($htmlToRender);
    }

    #[Route('/image_delete/{image}', name: 'image_delete')]
    public function imageDelete(Image $image, Request $request, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($image);
        $entityManager->flush();
        $route = $request->headers->get('referer');
        $this->addFlash('success', 'Image Deleted !');

        return $this->redirect($route);
    }

    #[Route('/image_update', name: 'image_update')]
    public function imageUpdate(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $request->get('image_edit_form');
        $imageRepository = $entityManager->getRepository(Image::class);
        $trickRepository = $entityManager->getRepository(Trick::class);
        $entityManager->remove($imageRepository->find($form['hidden']));

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

        $entityManager->persist($image);
        $entityManager->flush();

        $route = $request->headers->get('referer');
        $this->addFlash('success', 'Image Update !');

        return $this->redirect($route);
    }
}
