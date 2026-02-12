<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Service\RabbitMQService;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class AnecdoteConsumer
{
    public function __construct(
        private RabbitMQService          $rabbit,
        private AnecdotePublisherService $anecdoteService,
        private LoggerInterface          $logger,
    ) {}
    public function run() : void
    {
        $connection = $this->rabbit->getConnection();
        $channel = $this->rabbit->setupChannel($connection);

        $this->logger->info("Anecdote consumer started");

        $channel->basic_consume(
        $this->rabbit->getQueue(),
        '' ,
            false,
            false,
            false,
            false,
            fn (AMQPMessage $msg) => $this->handle($msg)
        );
        while ($channel->is_open()) {
            $channel->wait();
        }
    }
    private function handle(AMQPMessage $msg) : void
    {
        try {
            $data = json_decode($msg->getBody(), true);

            $this->anecdoteService->createFromMessage($data);

            $msg->ack();
        } catch (\Throwable $e) {
            $this->logger->error('Consumer error', [
                'error' => $e->getMessage(),
                'message' => $msg->getBody()
            ]);
            $msg->nack(false, true);
        }
    }
}
