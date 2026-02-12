<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\Services\VoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class VoteController extends AbstractController
{
    public function __construct(private VoteService $voteService)
    {
    }

    #[Route('/anecdote/{id}/like', name: 'anecdote_like', methods: ['POST'])]
    public function like(
        Anecdote                  $anecdote,
        Request                   $request,
        CsrfTokenManagerInterface $csrfManager
    ): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfManager->isTokenValid(new CsrfToken('vote', $token))) {
            return $this->json(['requireLogin' => true], 403);
        }

        $user = $this->getUser();
        if (!$user) return $this->json(['requireLogin' => true], 403);

        $count = $this->voteService->likeAnecdote($anecdote, $user);

        return $this->json(['votes' => $count]);
    }
}
