<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
{
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
