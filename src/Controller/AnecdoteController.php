<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\Entity\User;
use App\Repository\TopicRepository;
use App\Services\AnecdoteApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnecdoteController extends AbstractController
{
    public function __construct(private AnecdoteApiService $service)
    {
    }

    #[Route('/anecdotes', name: 'anecdote_index')]
    public function index(TopicRepository $topicRepo, Request $request): Response
    {
        $topics = $topicRepo->findAll();

        $currentTopicId = $request->query->get('topic_id');

        return $this->render('anecdote/index.html.twig', [
            'topics' => $topics,
            'currentTopicId' => $currentTopicId,
        ]);
    }

    #[Route('/anecdote/add', name: 'anecdote_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 401);
        }
        $data = json_decode($request->getContent(), true);
        $this->service->createAnecdote($user, $data['text'] ?? '', $data['topic_id'] ?? null, $data['currentTopicId'] ?? null);
        return $this->json(['success' => true]);
    }
}
