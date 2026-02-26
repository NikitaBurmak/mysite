<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;


class RegistrationService
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $hasher,
        private Security                    $security
    )
    {
    }

    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['success' => false, 'error' => 'Email и пароль обязательны'], 400);
        }

        if ($this->userRepository->findOneBy(['email' => $email])) {
            return new JsonResponse(['success' => false, 'error' => 'Пользователь с таким email уже существует'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        $this->security->login($user);

        return new JsonResponse([
            'success' => true,
            'roles' => $user->getRoles(),
            'user' => [
                'email' => $user->getEmail(),
                'id' => $user->getId(),
            ]
        ]);
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('Wrong login');
        }

        if (!$this->hasher->isPasswordValid($user, $password)) {
            throw new \Exception('Wrong login');
        }

        $this->security->login($user);

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];

    }
}
