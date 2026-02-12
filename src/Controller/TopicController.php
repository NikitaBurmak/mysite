<?php

namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Services\TopicService;

class TopicController extends AbstractController
{
    #[Route('/topics', name: 'topic_index')]
    public function index(TopicService $topicService): Response
    {
        $topics = $topicService->getAllTopics();
        return $this->render('topic/index.html.twig', ['topics' => $topics]);
    }
}
