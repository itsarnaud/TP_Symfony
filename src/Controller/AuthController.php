<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $req, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $passwordHasher, 
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setRoles(['ROLE_USER']);
        $user->setPhoneNumber($data['phoneNumber']);

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur créé avec succès',], 201);
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $JWTManager, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Email ou mot de passe incorrect.'], 401);
        }

        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
   
}
