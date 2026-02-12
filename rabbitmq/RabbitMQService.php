<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQService
{
    private string $host;
    private int $port;
    private string $user;
    private string $password;
    private string $exchange;
    private string $queue;
    private string $routingKey;

    public function __construct()
    {
        $this->host = $_ENV['RABBITMQ_HOST'] ?? 'localhost';
        $this->port = (int)$_ENV['RABBITMQ_PORT'] ?? 5672;
        $this->user = $_ENV['RABBITMQ_USER'] ?? 'guest';
        $this->password = $_ENV['RABBITMQ_PASS'] ?? 'guest';
        $this->exchange = $_ENV['RABBIT_EXCHANGE'] ?? 'default_exchange';
        $this->queue = $_ENV['RABBIT_QUEUE'] ?? 'default_queue';
        $this->routingKey = $_ENV['RABBIT_ROUTING_KEY'] ?? 'default_routing';
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
