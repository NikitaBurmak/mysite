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
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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

        try {
            $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
            $channel = $connection->channel();

            $channel->exchange_declare('anecdote_topic', 'topic', false, true, false);
            $channel->queue_declare('test_queue', false, true, false, false);
            $channel->queue_bind('test_queue', 'anecdote_topic', 'anecdote.create');

            $data = [
                'text' => $anecdote->getText(),
                'id' => $anecdote->getId(),
                'topic' => $anecdote->getTopic()->getName(),
                'user' => $user->getId()
            ];

            $msg = new AMQPMessage(json_encode($data), [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            ]);
            $channel->basic_publish($msg, 'anecdote_topic', 'anecdote.create');

            $channel->close();
            $connection->close();

        } catch (\Throwable $e) {
            file_put_contents(__DIR__ . '/../../var/log/rabbit.log', $e->getMessage() . "\n", FILE_APPEND);
        }

        return $this->json([
            'success' => true,
            'id' => $anecdote->getId(),
            'text' => $anecdote->getText(),
            'topic' => $anecdote->getTopic()->getName(),
            'votesSum' => $anecdote->getVotesSum(),
        ]);
    }

    #[Route('/api/anecdotes', name: 'api_anecdotes', methods: ['GET'])]
    public function apiAnecdotes(EntityManagerInterface $em): JsonResponse
    {
        $anecdotes = $em->getRepository(Anecdote::class)->findAll();
        $data = [];
        foreach ($anecdotes as $a) {
            $data[] = [
                'id' => $a->getId(),
                'text' => $a->getText(),
                'topic' => $a->getTopic()->getName(),
                'votesSum' => $a->getVotesSum(),
            ];
        }
        return $this->json($data);
    }
}
