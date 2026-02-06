<?php

namespace App\Controller\Api;

use App\Entity\Anecdote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiAnecdoteController extends AbstractController
{
    #[Route('/anecdotes', name: 'api_anecdotes', methods: ['GET'])]
    public function index(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $topicId = $request->query->get('topic');

        $anecdotes = $topicId
            ? $em->getRepository(Anecdote::class)->findBy(['topic' => $topicId])
            : $em->getRepository(Anecdote::class)->findAll();

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
