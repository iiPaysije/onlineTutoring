<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setEmail($form->get('email')->getData());
            $user->setRoles($form->get('roles')->getData());

            // Save user to database
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('/dashboard');
        }

        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }
}
