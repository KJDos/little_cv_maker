<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\Realisation;
use App\Form\ProfileType;
use App\Form\RealisationType;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin", name="admin")
     */
    public function index(Request $request, EntityManagerInterface $manager, ProfileRepository $profileRepo): Response
    {
        $profiles = $profileRepo->findAll();
        if(empty($profiles)){
            $profile = new Profile();
            $profileForm = $this->createForm(ProfileType::class, $profile);
        }else{
            //$profiles[0]->getId()
        }

        if(isset($profileForm)){
            $profileForm->handleRequest($request);
            if ($profileForm->isSubmitted() && $profileForm->isValid()) {
                $manager->persist($profile);
                $manager->flush();

                return $this->redirectToRoute('admin');
            }

            return $this->render('admin/index.html.twig', [
                'profileForm' => $profileForm->createView(),
            ]);
        }

        return $this->render('admin/index.html.twig', [
            'profiles' => $profiles,
        ]);

    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/profile/{id}", name="admin_edit_profile")
     * @return void
     */
    public function editProfile(Profile $profile, Request $request, EntityManagerInterface $manager, ProfileRepository $profileRepo)
    {
        $editProfileForm = $this->createForm(ProfileType::class, $profile);
        $editProfileForm->handleRequest($request);
        if ($editProfileForm->isSubmitted() && $editProfileForm->isValid()) {
           $manager->persist($profile);
           $manager->flush();
        }

        return $this->render('admin/editProfile.html.twig', [
            'editProfileForm' => $editProfileForm->createView(),
            'profile' => $profile,
        ]);
    }

}

