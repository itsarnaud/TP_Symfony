<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setRoles($data['roles']);
        $user->setPhoneNumber($data['phoneNumber']);

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], 201);
    }

    #[Route('', name: 'list_users', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $json = $serializer->serialize($users, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'read_user', methods: ['GET'])]
    public function readUser(User $user, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, User $user, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setRoles($data['roles']);
        $user->setPhoneNumber($data['phoneNumber']);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur mis à jour avec succès'], 200);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur supprimé avec succès'], 200);
    }
}