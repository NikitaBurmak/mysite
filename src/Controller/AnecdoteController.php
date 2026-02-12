<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\Entity\User;
use App\Repository\AnecdoteRepository;
use App\Repository\TopicRepository;
use App\Services\AnecdoteApiService;
use App\Services\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnecdoteController extends AbstractController
{
    public function __construct(private AnecdoteApiService $service) {}

    #[Route('/anecdotes', name: 'anecdote_index')]
    public function index(): Response
    {
        $anecdotes = $this->service->getAllAnecdotes();
        return $this->render('anecdote/index.html.twig', ['anecdotes' => $anecdotes]);
    }

    #[Route('/anecdote/add', name: 'anecdote_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['success' => false, 'error' => 'Вы должны войти'], 403);
        }

        try {
            $anecdote = $this->service->createAnecdote(
                $user,
                $request->request->get('text'),
                (int)$request->request->get('topic_id')
            );

            return $this->json([
                'success' => true,
                'id' => $anecdote->getId(),
                'text' => $anecdote->getText(),
                'topic' => $anecdote->getTopic()->getName(),
                'votesSum' => $anecdote->getVotesSum(),
            ]);
        } catch (\DomainException $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
