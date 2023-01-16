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

}
