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
    public function index(Request $request, EntityManagerInterface $manager, ProfileRepository $profileRepo, RealisationRepository $realisationRepo): Response
    {

        $realisation = new Realisation();
        $realisationForm = $this->createForm(RealisationType::class, $realisation);
        $realisations = $realisationRepo->findAll();
        //profile
        $profiles = $profileRepo->findAll();
        if(empty($profiles)){
            $profile = new Profile();
            $profileForm = $this->createForm(ProfileType::class, $profile);
        }

        if(isset($profileForm)){
            $profileForm->handleRequest($request);
            if ($profileForm->isSubmitted() && $profileForm->isValid()) {

                $photoFile = $profileForm->get('photo')->getData();

                if ($photoFile) {

                    $filename = $profileForm->get('prenom')->getData().'.'.$photoFile->guessExtension();


                    try {
                        $photoFile->move(
                            $this->getParameter('photos_directory'),
                            $filename
                        );
                    } catch (FileException $e) {
                        dump($e);
                        // ... handle exception if something happens during file upload = redirect + appflash error
                    }
                    $profile->setPhoto($filename);
                } else {
                    $defaultPhoto = 'no-photo.jpg';
                    $profile->setPhoto($defaultPhoto);
                }

                if($profileForm->get('telephone')->getData()){
                    $telephone = $profileForm->get('telephone')->getData();
                    $profile->setTelephone(substr($telephone,1));
                }
                if ($profileForm->get('telephone_alt')->getData()) {
                    $telephoneAlt = $profileForm->get('telephone_alt')->getData();
                    $profile->setTelephoneAlt(substr($telephoneAlt, 1));
                }


                $manager->persist($profile);
                $manager->flush();

                return $this->redirectToRoute('admin');
            }

            return $this->render('admin/index.html.twig', [
                'profileForm' => $profileForm->createView(),
                'realisationForm' => $realisationForm->createView(),
                'realisations' => $realisations,
            ]);
        }

        //Ajouter une realisation
        //$realisation = new Realisation();
        //$realisationForm = $this->createForm(RealisationType::class, $realisation);
        $realisationForm->handleRequest($request);
        if($realisationForm->isSubmitted() && $realisationForm->isValid()){

            $photoFileIllustration = $realisationForm->get('illustration')->getData();
            if ($photoFileIllustration) {
                $filename = $realisationForm->get('title')->getData() . '.' . $photoFileIllustration->guessExtension();
                try {
                    $photoFileIllustration->move(
                        $this->getParameter('photos_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    dump($e);
                    // ... handle exception if something happens during file upload = redirect + appflash error
                }
                $realisation->setIllustration($filename);
            } else {
                $defaultPhoto = 'no-illustration.jpg';
                $realisation->setIllustration($defaultPhoto);
            }

            $photoFileVignette = $realisationForm->get('vignette')->getData();
            if ($photoFileVignette) {
                $filename = $realisationForm->get('title')->getData() . '.' . $photoFileVignette->guessExtension();
                try {
                    $photoFileVignette->move(
                        $this->getParameter('photos_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    dump($e);
                    // ... handle exception if something happens during file upload = redirect + appflash error
                }
                $realisation->setVignette($filename);
            } else {
                $defaultPhoto = 'no-vignette.jpg';
                $realisation->setVignette($defaultPhoto);
            }

            $manager->persist($realisation);
            $manager->flush();
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/index.html.twig', [
            'profile' => $profiles[0],
            'realisations' => $realisations,
            'realisationForm' => $realisationForm->createView(),
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

            $photoFile = $editProfileForm->get('photo')->getData();

            if ($photoFile) {

                $filename = $editProfileForm->get('prenom')->getData() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload = redirect + appflash error
                }
                $profile->setPhoto($filename);
            }


            if ($editProfileForm->get('telephone')->getData()) {
                $telephone = $editProfileForm->get('telephone')->getData();
                $profile->setTelephone(substr($telephone, 1));
            }
            if ($editProfileForm->get('telephone_alt')->getData()) {
                $telephoneAlt = $editProfileForm->get('telephone_alt')->getData();
                $profile->setTelephoneAlt(substr($telephoneAlt, 1));
            }

            $manager->persist($profile);
            $manager->flush();
        }

        return $this->render('admin/editProfile.html.twig', [
            'editProfileForm' => $editProfileForm->createView(),
            'profile' => $profile,
        ]);
    }

}

