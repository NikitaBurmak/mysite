<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('anecdote_topic', 'topic', false, true, false);
$channel->queue_declare('test_queue', false, true, false, false);
$channel->queue_bind('test_queue', 'anecdote_topic', 'anecdote.create');

echo "Setup done\n";
$channel->close();
$connection->close();
