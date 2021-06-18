<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use App\Repository\RealisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProfileRepository $profileRepo, RealisationRepository $realisationRepo): Response
    {
        $profiles = $profileRepo->findAll();
        $realisations = $realisationRepo->findAll();
        if (!$profiles) {
            return $this->redirectToRoute('admin');
        }else{
            $profile = $profiles[0];
        }
        

        return $this->render('home/index.html.twig', [
            'profile' => $profile,
            'realisations' => $realisations,
        ]);
    }
}
