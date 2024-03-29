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
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/videoupload/{id}', name: 'videoupload')]
    public function videoUpload(Trick $trick, Request $request)
    {
        $video = new Video();
        $video->setUrl($request->get('url'))
            ->setTrick($trick)
            ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($video);
        $this->entityManager->flush();

        $htmlToRender = $this->renderView('media/video_list.html.twig', array(
            'video' => $video
        ));

        return new Response($htmlToRender);
    }

    #[Route('/video_delete/{video}', name: 'video_delete')]
    public function videoDelete(Video $video, Request $request)
    {
        $this->entityManager->remove($video);
        $this->entityManager->flush();
        $route = $request->headers->get('referer');
        $this->addFlash('success', 'Video Deleted !');

        return $this->redirect($route);
    }

    #[Route('/video_update', name: 'video_update')]
    public function imageUpdate(Request $request)
    {
        $form = $request->get('video_edit_form');
        $videoRepository = $this->entityManager->getRepository(Video::class);
        $trickRepository = $this->entityManager->getRepository(Trick::class);

        $video = $videoRepository->find($form['video_id']);
        $video->setUrl($form['url'])
            ->setTrick($trickRepository->find($form['trick_id']))
            ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($video);
        $this->entityManager->flush();

        $route = $request->headers->get('referer');
        $this->addFlash('success', 'Video Update !');

        return $this->redirect($route);
    }
}
