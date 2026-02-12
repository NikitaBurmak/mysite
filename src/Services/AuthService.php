<?php

namespace App\Services;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthService
{
    public function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function login(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user || !$this->hasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['success' => false, 'error' => 'Неверный email или пароль']);
        }

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $request->getSession()->set('_security_main', serialize($token));

        return new JsonResponse([
            'success' => true,
            'roles' => $user->getRoles(),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        ]);
    }
}
