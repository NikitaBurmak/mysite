<?php

namespace App\Services;

use App\Entity\User;
use App\Exception\InvalidEmailException;
use App\Exception\InvalidPasswordException;
use Doctrine\ORM\EntityManagerInterface;
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

    public function register(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return ['success' => false, 'error' => 'Email и пароль обязательны'];
        }

        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Неверный формат email'];
        }

        // Валидация пароля - минимум 6 символов
        if (strlen($password) < 6) {
            return ['success' => false, 'error' => 'Пароль должен быть минимум 6 символов'];
        }

        if ($this->userRepository->findOneBy(['email' => $email])) {
            return ['success' => false, 'error' => 'Пользователь с таким email уже существует'];
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        $this->security->login($user);

        return [
            'success' => true,
            'roles' => $user->getRoles(),
            'user' => [
                'email' => $user->getEmail(),
                'id' => $user->getId(),
            ]
        ];
    }

    public function login(string $email, string $password): array
    {
        if (!$email || !$password) {
            throw new \InvalidArgumentException('Email и пароль обязательны');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new InvalidEmailException('Email не найден');
        }

        if (!$this->hasher->isPasswordValid($user, $password)) {
            throw new InvalidPasswordException('Неверный пароль');
        }

        $this->security->login($user);

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];

    }
}
