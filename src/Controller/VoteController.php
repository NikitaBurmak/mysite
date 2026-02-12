<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class VoteController extends AbstractController
{
    #[Route('/anecdote/{id}/like', name: 'anecdote_like', methods: ['POST'])]
    public function like(
        Anecdote                  $anecdote,
        EntityManagerInterface    $em,
        VoteRepository            $voteRepo,
        CsrfTokenManagerInterface $csrfManager,
        Request                   $request
    ): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if (!$csrfManager->isTokenValid(new CsrfToken('vote', $token))) {
            return $this->json(['requireLogin' => true], 403);
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['requireLogin' => true], 403);
        }
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['requireLogin' => true], 403);
        }

        $existingVote = $voteRepo->findOneBy([
            'anecdote' => $anecdote,
            'user' => $user
        ]);

        if ($existingVote) {
            $count = $voteRepo->count(['anecdote' => $anecdote]);
            return $this->json(['votes' => $count]);
        }

        $vote = new Vote();
        $vote->setUser($user);
        $vote->setAnecdote($anecdote);
        $em->persist($vote);
        $em->flush();

        $count = $voteRepo->count(['anecdote' => $anecdote]);

        return $this->json(['votes' => $count]);
    }
}
