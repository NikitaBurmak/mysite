<?php

namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TopicController extends AbstractController
{
    #[Route('/topics', name: 'topic_index')]
    public function index(
        TopicRepository $topicRepository,
        CacheInterface $cache
    ): Response {
        $topics = $cache->get('topics_list', function (ItemInterface $item) use ($topicRepository) {
            $item->expiresAfter(3600); // 1 час
            return $topicRepository->findAll();
        });

        return $this->render('topic/index.html.twig', [
            'topics' => $topics,
        ]);
    }
}
