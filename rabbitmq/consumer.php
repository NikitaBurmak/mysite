<?php
require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED);

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('anecdote_topic', 'topic', false, true, false);
$channel->queue_declare('test_queue', false, true, false, false);
$channel->queue_bind('test_queue', 'anecdote_topic', 'anecdote.create');

echo " [*] Waiting for messages.\n";

$channel->basic_consume('test_queue', '', false, false, false, false, function ($msg) {
    try {
        $data = json_decode($msg->body, true);
        if (!is_array($data) || !array_key_exists('text', $data)) {
            echo "Invalid message: " . $msg->body . "\n";
            $msg->getChannel()->basic_nack($msg->delivery_info['delivery_tag'], false, true);
            return;
        }
        echo "Received anecdote: " . $data['text'] . "\n";
        $msg->getChannel()->basic_ack($msg->delivery_info['delivery_tag']);
    } catch (\Throwable $e) {
        echo 'Error procesing message: ' . $e->getMessage() . "\n";
        $msg->getChannel()->basic_nack($msg->delivery_info['delivery_tag'], false, true);
    }
});

while ($channel->is_open()) {
    try {
        $channel->wait();
    } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
    } catch (\Throwable $e) {
        echo "Consumer error: " . $e->getMessage() . "\n";
        sleep(1);
    }
}
