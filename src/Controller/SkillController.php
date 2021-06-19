<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Form\SkillType;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SkillController extends AbstractController
{
    /**
     * IsGranted("ROLE_USER")
     * @Route("admin/skill/new", name="new_skill")
     */
    public function newSkill(Request $request, EntityManagerInterface $manager, SkillRepository $skillRepo)
    {
        $skills = $skillRepo->findAll();
        $skill = new Skill();
        $newSkillForm = $this->createForm(SkillType::class, $skill);
        $newSkillForm->handleRequest($request);

        if($newSkillForm->isSubmitted() && $newSkillForm->isValid()){
            $manager->persist($skill);
            $manager->flush();
            
            return $this->redirectToRoute('new_skill');
        }

        return $this->render('admin/skill/new_skill.html.twig', [
            'newSkillForm' => $newSkillForm->createView(),
            'skills' => $skills,
        ]);

    }

    /**
     * IsGranted("ROLE_USER")
     * @Route("admin/skill/{id}", name="edit_skill")
     */
    public function editSkill(Skill $skill, EntityManagerInterface $manager, Request $request)
    {
        $editSkillForm = $this->createForm(SkillType::class, $skill);
        $editSkillForm->handleRequest($request);

        if($editSkillForm->isSubmitted() && $editSkillForm->isValid()){
            $manager->persist($skill);
            $manager->flush();
            
            return $this->redirectToRoute('new_skill');
        }

        return $this->render('admin/skill/edit_skill.html.twig',[
            'editSkillForm' => $editSkillForm->createView(),
            'skill' => $skill,
        ]);
    }

    /**
     * IsGranted("ROLE_USER")
     * @Route("admin/skill/remove/{id}", name="remove_skill")
     */
    public function removeSkill($id, SkillRepository $skillRepo, EntityManagerInterface $manager)
    {
        $skill = $skillRepo->find($id);
        $manager->remove($skill);
        $manager->flush();

        return $this->redirectToRoute('new_skill');
    }
}
