<?php

namespace App\Controller;

use App\Entity\Anecdote;
use App\services\AnecdoteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(
        private AnecdoteService        $service,
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $anecdotes = $this->em->getRepository(Anecdote::class)->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'anecdotes' => $anecdotes,
        ]);
    }

    #[Route('/admin/anecdote/save', name: 'admin_anecdote_save', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->service - updateText(
                (int)$request -> request - get('id'),
                $request -> request - get('text'),
            );
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['success' => false , 'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('/admin/anecdote/delete/{id}', name: 'admin_anecdote_delete', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->service->delete($id);
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['success' => false]);
        }
    }
}
