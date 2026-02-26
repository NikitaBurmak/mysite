<?php

namespace App\Services\RabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

class AnecdotePublisherService
{
    public function __construct(
        private RabbitMQService $rabbit,
    )
    {
    }

    public function publish(string $text, int $userId, ?int $topicId = null): void
    {
        $connection = $this->rabbit->getConnection();
        $channel = $this->rabbit->setupChannel($connection);

        $data = [
            'text' => $text,
            'userId' => $userId,
            'topicId' => $topicId,
        ];

        $msg = new AMQPMessage(
            json_encode($data),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $channel->basic_publish($msg, $this->rabbit->getExchange(), $this->rabbit->getRoutingKey());

        $channel->close();
        $connection->close();
    }
}
