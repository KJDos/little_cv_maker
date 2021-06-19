<?php

namespace App\Controller;

use App\Entity\Language;
use App\Form\LanguageType;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguageController extends AbstractController
{
    /**
     * IsGranted("ROLE_USER")
     * @Route("/admin/language/new", name="new_language")
     */
    public function newLanguage(Request $request, EntityManagerInterface $manager, LanguageRepository $languageRepo)
    {
        $languages = $languageRepo->findAll();
        $language = new Language();
        $newLanguageForm =  $this->createForm(LanguageType::class, $language);
        $newLanguageForm->handleRequest($request);

        if($newLanguageForm->isSubmitted() && $newLanguageForm->isValid()){
            $manager->persist($language);
            $manager->flush();

            return $this->redirectToRoute('new_language');
        }
        return $this->render('admin/language/new_language.html.twig', [
            'newLanguageForm' => $newLanguageForm->createView(),
            'languages' => $languages,
        ]);
    }

    /**
     * IsGranted("ROLE_USER")
     * @Route("/admin/language/{id}", name="edit_language")
     */
    public function editLanguage(Language $language,Request $request, EntityManagerInterface $manager, LanguageRepository $languageRepo)
    {
        $editLanguageForm = $this->createForm(LanguageType::class, $language);
        $editLanguageForm->handleRequest($request);

        if ($editLanguageForm->isSubmitted() && $editLanguageForm->isValid()) {
            $manager->persist($language);
            $manager->flush();

            return $this->redirectToRoute('new_language');
        }

        return $this->render('admin/language/edit_language.html.twig', [
            'editLanguageForm' => $editLanguageForm->createView(),
            'language' => $language,
        ]);
    }

    /**
     * IsGranted("ROLE_USER")
     * @Route("/admin/language/remove/{id}", name="remove_language")
     */
    public function removeLanguage($id, EntityManagerInterface $manager, LanguageRepository $languageRepo){
        $language = $languageRepo->find($id);
        $manager->remove($language);
        $manager->flush();

        return $this->redirectToRoute('new_language');
    }
}
