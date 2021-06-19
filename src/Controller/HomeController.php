<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\LanguageRepository;
use App\Repository\ProfileRepository;
use App\Repository\RealisationRepository;
use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProfileRepository $profileRepo, RealisationRepository $realisationRepo, SkillRepository $skillRepo, FormationRepository $formationRepo, LanguageRepository $languageRepo): Response
    {
        $profiles = $profileRepo->findAll();
        $realisations = $realisationRepo->findAll();
        $skills = $skillRepo->findAll();
        $formations = $formationRepo->findAll();
        $languages = $languageRepo->findAll();

        if (empty($profiles)) {
            return $this->redirectToRoute('admin');
        }else{
            $profile = $profiles[0];
        }
        

        return $this->render('home/index.html.twig', [
            'profile' => $profile,
            'realisations' => $realisations,
            'skills' => $skills,
            'formations' => $formations,
            'languages' => $languages,
        ]);
    }
}
