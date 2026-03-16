<?php

namespace App\Controller;

use App\Controller\Trait\AuthenticationTrait;
use App\Entity\Anecdote;
use App\Entity\User;
use App\Services\VoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    use AuthenticationTrait;

    public function __construct(private VoteService $voteService)
    {
    }

    #[Route('/anecdote/{id}/like', name: 'anecdote_like', methods: ['POST'])]
    public function like(Anecdote $anecdote): JsonResponse
    {
        $user = $this->getAppUser();

        if (!$user) {
            return $this->json(['requireLogin' => true], 401);
        }

        $count = $this->voteService->likeAnecdote($anecdote, $user);

        return $this->json(['votes' => $count]);
    }
}
