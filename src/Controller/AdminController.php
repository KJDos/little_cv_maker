<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Entity\Realisation;
use App\Form\RealisationType;
use App\Repository\ProfileRepository;
use App\Repository\RealisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin", name="admin")
     */
    public function index(ProfileRepository $profileRepo, RealisationRepository $realisationRepo): Response
    {

        $profiles = $profileRepo->findAll();
        if(!empty($profiles)){
            $profile = $profiles[0];
        }else{
            $profile = [];
        }

        $realisations = $realisationRepo->findAll();


        return  $this->render('admin/index_admin.html.twig', [
            'profile' => $profile,
            'realisations' => $realisations,
        ]);

    }

}