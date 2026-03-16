<?php

namespace App\Controller;

use App\Controller\Trait\AuthenticationTrait;
use App\Services\AnecdoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    use AuthenticationTrait;

    public function __construct(
        private AnecdoteService $service,
    )
    {
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->requireAdmin();

        $anecdotes = $this->service->getAllAnecdotes();

        return $this->render('admin/dashboard.html.twig', compact('anecdotes'));
    }

    #[Route('/admin/anecdote/save', name: 'admin_anecdote_save', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $this->requireAdmin();

        try {
            $this->service->updateText(
                (int)$request->request->get('id'),
                $request->request->get('text')
            );
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    #[Route('/admin/anecdote/delete/{id}', name: 'admin_anecdote_delete', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        $this->requireAdmin();

        try {
            $this->service->delete($id);
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['success' => false]);
        }
    }
}
