<?php

namespace App\Controller;

use App\Entity\Anecdote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
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
    public function save(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $request->request->get('id');
        $text = $request->request->get('text');

        if (!$id || !$text) {
            return $this->json(['success' => false, 'error' => 'Не переданы данные']);
        }

        $anecdote = $em->getRepository(Anecdote::class)->find($id);
        if (!$anecdote) {
            return $this->json(['success' => false, 'error' => 'Анекдот не найден']);
        }

        $anecdote->setText($text);
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/admin/anecdote/delete/{id}', name: 'admin_anecdote_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $anecdote = $em->getRepository(Anecdote::class)->find($id);
            if (!$anecdote) {
                return $this->json(['success' => false, 'error' => 'Анекдот не найден']);
            }
        $this->em->remove($anecdote);
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
