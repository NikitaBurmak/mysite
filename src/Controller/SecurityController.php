<?php

namespace App\Controller;

use App\Services\AuthService;
use App\Services\RegistrationService;
use App\Services\AnecdoteApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Anecdote;
use App\Entity\Topic;
use App\Repository\TopicRepository;

class SecurityController extends AbstractController
{

    public function __construct(
        private RegistrationService $registrationService,
        private AuthService         $authService,
        private AnecdoteApiService  $anecdoteService
    )
    {
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $anecdotes = $this->anecdoteService->getAllAnecdotes();
        $topics = $this->anecdoteService->getAllTopics();

        return $this->render('anecdote/index.html.twig', [
            'anecdotes' => $anecdotes,
            'topics' => $topics,
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, CsrfTokenManagerInterface $csrfManager): JsonResponse
    {
        $csrfToken = $request->request->get('_csrf_token');
        if (!$csrfManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            return $this->json(['success' => false, 'error' => 'CSRF токен невалиден'], 400);
        }

        return $this->authService->login($request);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, CsrfTokenManagerInterface $csrfManager): JsonResponse
    {
        $csrfToken = $request->request->get('_csrf_token');
        if (!$csrfManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            return $this->json(['success' => false, 'error' => 'CSRF токен невалиден'], 400);
        }

        return $this->registrationService->register($request);
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
        if (!$user) return $this->json(null);

        return $this->json([
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
