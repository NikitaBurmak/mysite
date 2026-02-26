<?php

namespace App\Command;

use App\Services\RabbitMQ\AnecdoteConsumer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:consume-anecdotes',
    description: 'Запускает консумер анекдотов из RabbitMQ'
)]
class ConsumeAnecdotesCommand extends Command
{
    public function __construct(private AnecdoteConsumer $consumer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->consumer->run();

        return Command::SUCCESS;
    }
}
