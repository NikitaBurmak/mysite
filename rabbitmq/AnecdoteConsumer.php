<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Service\RabbitMQService;

$service = new RabbitMQService();
$connection = $service->getConnection();
$channel = $service->setupChannel($connection);

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
