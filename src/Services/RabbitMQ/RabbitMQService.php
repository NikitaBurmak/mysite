<?php

namespace App\Services\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQService
{
    public function __construct(
        private string $host,
        private int    $port,
        private string $user,
        private string $password,
        private string $exchange,
        private string $queue,
        private string $routingKey
    )
    {
    }

    public function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
    }

    public function setupChannel(AMQPStreamConnection $connection): AMQPChannel
    {
        $channel = $connection->channel();
        $channel->exchange_declare($this->exchange, 'topic', false, true, false);
        $channel->queue_declare($this->queue, false, true, false, false);
        $channel->queue_bind($this->queue, $this->exchange, $this->routingKey);

        return $channel;
    }

    public function getExchange(): string //если я захочу логировать или динамически менять очереди и ключи
    {
        return $this->exchange;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }
}
