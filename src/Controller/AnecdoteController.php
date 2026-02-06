<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\Entity\User;
use App\Repository\AnecdoteRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnecdoteController extends AbstractController
{
    #[Route('/anecdotes', name: 'anecdote_index')]
    public function index(
        AnecdoteRepository $anecdoteRepository,
        TopicRepository    $topicRepository
    ): Response
    {
        $anecdotes = $anecdoteRepository->findAll();
        $topics = $topicRepository->findAll();

        return $this->render('anecdote/index.html.twig', [
            'anecdotes' => $anecdotes,
            'topics' => $topics,
        ]);
    }

    #[Route('/anecdote/add', name: 'anecdote_add', methods: ['POST'])]
    public function add(
        Request                $request,
        EntityManagerInterface $em,
        TopicRepository        $topicRepository
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['success' => false, 'error' => 'Вы должны войти'], 403);
        }

        $text = $request->request->get('text');
        $topicId = $request->request->get('topic_id');

        if (!$text) {
            return $this->json(['success' => false, 'error' => 'Пустой анекдот'], 400);
        }

        $topic = $topicRepository->find($topicId);
        if (!$topic) {
            return $this->json(['success' => false, 'error' => 'Тема не найдена'], 400);
        }

        $anecdote = new Anecdote();
        $anecdote->setText($text);
        $anecdote->setTopic($topic);
        $anecdote->setUser($user);

        $em->persist($anecdote);
        $em->flush();

        return $this->json([
            'success' => true,
            'id' => $anecdote->getId(),
            'text' => $anecdote->getText(),
            'topic' => $anecdote->getTopic()->getName(),
            'votesSum' => $anecdote->getVotesSum(),
        ]);
    }
}
