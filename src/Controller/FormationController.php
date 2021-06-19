<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/formation/new", name="new_formation")
     */
    public function newFormation(Request $request, EntityManagerInterface $manager, FormationRepository $formationRepo): Response
    {
        $formations = $formationRepo->findAll();
        $formation = new Formation();
        $newFormationForm = $this->createForm(FormationType::class, $formation);

        $newFormationForm->handleRequest($request);
        if($newFormationForm->isSubmitted() && $newFormationForm->isValid()){
            $manager->persist($formation);
            $manager->flush();

            return $this->redirectToRoute('new_formation');
        }


        return $this->render('admin/formation/new_formation.html.twig', [
            'newFormationForm' => $newFormationForm->createView(),
            'formations' => $formations,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/formation/{id}", name="edit_formation")
     */
    public function editFormation(Formation $formation, EntityManagerInterface $manager, Request $request)
    {
        $editFormationForm = $this->createForm(FormationType::class, $formation);
        $editFormationForm->handleRequest($request);

        if ($editFormationForm->isSubmitted() && $editFormationForm->isValid()) {
            $manager->persist($formation);
            $manager->flush();

            return $this->redirectToRoute('new_formation');
        }

        return $this->render('admin/formation/edit_formation.html.twig', [
            'editFormationForm' => $editFormationForm->createView(),
            'formation' => $formation,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/formation/remove/{id}", name="remove_formation")
     */
    public function removeFormation($id, FormationRepository $formationRepo, EntityManagerInterface $manager)
    {
        $formation = $formationRepo->find($id);
        $manager->remove($formation);
        $manager->flush();

        return $this->redirectToRoute('new_formation');
    }
}
