<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpController extends AbstractController
{
    /**
     * @Route("/signup", name="sign_up")
     */
    public function index(EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $userCount = $manager->createQuery('SELECT COUNT(user.id) FROM App\Entity\User user')->getSingleScalarResult();
        dump($userCount);
        $user = new User();
        $loginForm = $this->createForm(UserType::class, $user);

        $loginForm->handleRequest($request);
        if ($loginForm->isSubmitted() && $loginForm->isValid()) {

            if ($userCount == 0) {
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('login');
            } else {
                return $this->redirectToRoute('home');
            }
        }

        if ($userCount == 0){
            return $this->render('security/signup.html.twig', [
                'loginForm' => $loginForm->createView(),
            ]);
        }else{
            return $this->redirectToRoute('login');
        }

    }
}


