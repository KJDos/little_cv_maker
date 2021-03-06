<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{

    /**
     * @IsGranted("ROLE_USER")
     * @Route("admin/profile/new", name="new_profile")
     */
    public function newProfile(Request $request, EntityManagerInterface $manager, ProfileRepository $profileRepo)
    {

        //profile
        $profiles = $profileRepo->findAll();
        if (empty($profiles)) {
            $profile = new Profile();
            $profileForm = $this->createForm(ProfileType::class, $profile);
            $profileForm->handleRequest($request);

            if ($profileForm->isSubmitted() && $profileForm->isValid()) {

                if ($profileForm->get('photo')->getData()) {
                    $photoFile = $profileForm->get('photo')->getData();
                    $filename = $profileForm->get('prenom')->getData() . '.' . $photoFile->guessExtension();
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
                }

                if ($profileForm->get('pdf')->getData()) {
                    $pdfFile = $profileForm->get('pdf')->getData();
                    $filename = $profileForm->get('prenom')->getData() . '.' . $pdfFile->guessExtension();
                    try {
                        $pdfFile->move(
                            $this->getParameter('pdf_directory'),
                            $filename
                        );
                    } catch (FileException $e) {
                        dump($e);
                        // ... handle exception if something happens during file upload = redirect + appflash error
                    }
                    $profile->setPdf($filename);
                }


                //if ($profileForm->get('telephone')->getData()) {
                //    $telephone = $profileForm->get('telephone')->getData();
                //    $profile->setTelephone(substr($telephone, 1));
                //}
                //if ($profileForm->get('telephone_alt')->getData()) {
                //    $telephoneAlt = $profileForm->get('telephone_alt')->getData();
                //    $profile->setTelephoneAlt(substr($telephoneAlt, 1));
                //}
                $manager->persist($profile);
                $manager->flush();

                return $this->redirectToRoute('admin');
            }

            return $this->render('admin/profile/new_profile.html.twig', [
                'profileForm' => $profileForm->createView()
            ]);
        } else {
            //"Vous ne pouvez cr??er qu'un profil... pour l'instant"
            return $this->redirectToRoute('admin');
        }
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/profile/{id}", name="edit_profile")
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

            if ($editProfileForm->get('pdf')->getData()) {
                $pdfFile = $editProfileForm->get('pdf')->getData();
                $filename = $editProfileForm->get('prenom')->getData() . '-' . $editProfileForm->get('nom')->getData() . '.' . $pdfFile->guessExtension();
                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    dump($e);
                    // ... handle exception if something happens during file upload = redirect + appflash error
                }
                $profile->setPdf($filename);
            }

            //if ($editProfileForm->get('telephone')->getData()) {
            //    $telephone = $editProfileForm->get('telephone')->getData();
            //    $profile->setTelephone(substr($telephone, 1));
            //}
            //if ($editProfileForm->get('telephone_alt')->getData()) {
            //    $telephoneAlt = $editProfileForm->get('telephone_alt')->getData();
            //    $profile->setTelephoneAlt(substr($telephoneAlt, 1));
            //}

            $manager->persist($profile);
            $manager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/profile/edit_profile.html.twig', [
            'editProfileForm' => $editProfileForm->createView(),
            'profile' => $profile,
        ]);
    }

}
