<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\Services\VoteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    private VoteService $voteService;
    private EntityManagerInterface $em;

    public function __construct(VoteService $voteService, EntityManagerInterface $em)
    {
        $this->voteService = $voteService;
        $this->em = $em;
    }

    #[Route('/anecdote/{id}/like', name: 'anecdote_like', methods: ['POST'])]
    public function like(Anecdote $anecdote): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['requireLogin' => true], 401);
        }

        $count = $this->voteService->likeAnecdote($anecdote, $user);
        $this->em->flush();

        return $this->json(['votes' => $count]);
    }
}
