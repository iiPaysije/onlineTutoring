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
use function Sodium\add;

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

        if (isset($_POST['email'])) {
            $plainPassword = $_POST['password'];

            // Encode password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setEmail($_POST['email']);

            // user role must be array
            $roles_list = [];
            array_push($roles_list, $_POST['roles']);
            $user->setRoles($roles_list);

            // Save user to database
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }
}
