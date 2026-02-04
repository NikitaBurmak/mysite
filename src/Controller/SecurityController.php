<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function home(EntityManagerInterface $em, TopicRepository $topicRepository): Response
    {
        $anecdotes = $em->getRepository(Anecdote::class)->findAll();
        $topics = $em->getRepository(Topic::class)->findAll();


        return $this->render('anecdote/index.html.twig', [
            'anecdotes' => $anecdotes,
            'topics' => $topics,
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request                     $request,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $hasher,
        CsrfTokenManagerInterface   $csrfManager,
    ): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(['success' => false, 'error' => 'Только AJAX'], 400);
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        if (!$csrfManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            return $this->json(['success' => false, 'error' => 'CSRF токен невалиден'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user || !$hasher->isPasswordValid($user, $password)) {
            return $this->json(['success' => false, 'error' => 'Неверный email или пароль']);
        }

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $request->getSession()->set('_security_main', serialize($token));

        $redirect = in_array('ROLE_ADMIN', $user->getRoles(), true) ? '/admin' : '/';

        return $this->json([
            'success' => true,
            'roles' => $user->getRoles(),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ]);
    }

    #[
        Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request                     $request,
        EntityManagerInterface      $em,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $hasher,
        CsrfTokenManagerInterface   $csrfManager
    ): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(['success' => false, 'error' => 'Только AJAX'], 400);
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        if (!$csrfManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            return $this->json(['success' => false, 'error' => 'CSRF токен невалиден'], 400);
        }

        if (!$email || !$password) {
            return $this->json(['success' => false, 'error' => 'Email и пароль обязательны'], 400);
        }

        if ($userRepository->findOneBy(['email' => $email])) {
            return $this->json(['success' => false, 'error' => 'Пользователь с таким email уже существует'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
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

    #[Route('/login', name: 'app_login_get', methods: ['GET'])]
    public function loginPage(): Response
    {
        return $this->redirectToRoute('home');
    }

    #[Route('/api/current_user', name: 'api_current_user')]
    public function currentUser(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(null);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }
}
