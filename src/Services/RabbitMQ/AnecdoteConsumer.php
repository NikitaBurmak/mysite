<?php

namespace App\Services\RabbitMQ;

use App\Entity\Anecdote;
use App\Entity\Topic;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class AnecdoteConsumer
{
    public function __construct(
        private RabbitMQService        $rabbit,
        private EntityManagerInterface $em,
        private UserRepository         $userRepository,
        private TopicRepository        $topicRepository,
    )
    {
    }

    public function run(): void
    {
        $connection = $this->rabbit->getConnection();
        $channel = $this->rabbit->setupChannel($connection);

        $channel->basic_consume($this->rabbit->getQueue(),
            '',
            false,
            false,
            false,
            false,
            fn(AMQPMessage $msg) => $this->handle($msg)
        );

        while ($channel->is_open()) {
            $channel->wait();
        }
    }

    private function handle(AMQPMessage $msg): void
    {
        try {
            $data = json_decode($msg->getBody(), true);

            if (!$data || !isset($data['text'], $data['userId'])) {
                $msg->nack(false, false);
                return;
            }

            $user = $this->userRepository->find($data['userId']);
            if (!$user) {
                $msg->nack(false, false);
                return;
            }

            $topicIds = [];
            if (!empty($data['topicId'])) {
                $topicIds[] = $data['topicId'];
            }
            if (!empty($data['topicIds']) && is_array($data['topicIds'])) {
                $topicIds = array_merge($topicIds, $data['topicIds']);
            }

            $topics = [];
            foreach ($topicIds as $topicId) {
                $topic = $this->topicRepository->find($topicId);
                if ($topic) {
                    $topics[] = $topic;
                }
            }

            $anecdote = new Anecdote();
            $anecdote->setText($data['text']);
            $anecdote->setUser($user);

            foreach ($topics as $topic) {
                $anecdote->addTopic($topic);
            }

            $this->em->persist($anecdote);
            $this->em->flush();
            $msg->ack();
        } catch (\Throwable $e) {
            $msg->nack(false, true);
        }
    }
}
