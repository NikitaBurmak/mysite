<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\InvalidEmailException;
use App\Exception\InvalidPasswordException;
use App\Services\RegistrationService;
use App\Services\AnecdoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private RegistrationService $registrationService,
        private AnecdoteService     $anecdoteService
    )
    {
    }

    #[Route('/', name: 'home')]
    public function home(?int $topicId = null): Response
    {
        $anecdotes = $topicId
            ? $this->anecdoteService->getAnecdotesByTopic($topicId)
            : $this->anecdoteService->getAllAnecdotes();

        $topics = $this->anecdoteService->getAllTopics();

        return $this->render('anecdote/index.html.twig', [
            'anecdotes' => $anecdotes,
            'topics' => $topics,
            'currentTopicId' => $topicId,
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function loginPage(): Response
    {
        return $this->redirectToRoute('home');
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $user = $this->registrationService->login($data['email'] ?? '', $data['password'] ?? '');

            $redirect = in_array('ROLE_ADMIN', $user['roles'], true) ? '/admin' : '/';

            return $this->json(['success' => true, 'user' => $user, 'redirect' => $redirect]);
        } catch (InvalidEmailException|InvalidPasswordException $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 401);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => 'Ошибка аутентификации'], 401);
        }
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $result = $this->registrationService->register($request);

        if ($result['success'] ?? false) {
            return $this->json($result);
        }

        return $this->json($result, 400);
    }

    #[Route('/api/current_user', name: 'api_current_user')]
    public function currentUser(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(null);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \LogicException('This should never be called directly.');
    }
}
