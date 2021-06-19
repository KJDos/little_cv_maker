<?php

namespace App\Controller;

use App\Entity\Realisation;
use App\Form\RealisationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RealisationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RealisationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/realisation/new", name="new_realisation")
     */
    public function newRealisation(Request $request, EntityManagerInterface $manager)
    {
        $realisation = new Realisation();
        $realisationForm = $this->createForm(RealisationType::class, $realisation);
        $realisationForm->handleRequest($request);

        if ($realisationForm->isSubmitted() && $realisationForm->isValid()) {

            if ($realisationForm->get('vignette')->getData()) {
                $photoFile = $realisationForm->get('vignette')->getData();
                $filename = $realisationForm->get('title')->getData() . '.' . $photoFile->guessExtension();
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    dump($e);
                    // ... handle exception if something happens during file upload = redirect + appflash error
                }
                $realisation->setVignette($filename);
            }

            $manager->persist($realisation);
            $manager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/realisation/new_realisation.html.twig', [
            'realisationForm' => $realisationForm->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/realisation/{id}", name="edit_realisation")
     */
    public function editRealisation(Realisation $realisation, Request $request, EntityManagerInterface $manager)
    {
        $editRealisationForm = $this->createForm(RealisationType::class, $realisation);
        $editRealisationForm->handleRequest($request);

        if ($editRealisationForm->isSubmitted() && $editRealisationForm->isValid()) {
            if ($editRealisationForm->get('vignette')->getData()) {
                $photoFile = $editRealisationForm->get('vignette')->getData();
                $filename = $editRealisationForm->get('title')->getData() . '.' . $photoFile->guessExtension();
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    dump($e);
                    // ... handle exception if something happens during file upload = redirect + appflash error
                }
                $realisation->setVignette($filename);
            }
            $manager->persist($realisation);
            $manager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/realisation/edit_realisation.html.twig', [
            'editRealisationForm' => $editRealisationForm->createView(),
            'realisation' => $realisation,
        ]);
    }

    /**
     * IsGranted("ROLE_USER")
     * @Route("admin/realisation/remove/{id}", name="remove_realisation")
     */
    public function removeRealisation($id, RealisationRepository $repo, EntityManagerInterface $manager)
    {
        $realisation = $repo->find($id);
        $manager->remove($realisation);
        $manager->flush();

        return $this->redirectToRoute('admin');
    }

    
}
