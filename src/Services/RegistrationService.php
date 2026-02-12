<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationService
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function register(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

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

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
// Сохраняем токен в сессию
        $request->getSession()->set('_security_main', serialize($token));

        return new JsonResponse([
            'success' => true,
            'roles' => $user->getRoles(),
            'user' => [
                'email' => $user->getEmail(),
                'id' => $user->getId(),
            ]
        ]);
    }
}
