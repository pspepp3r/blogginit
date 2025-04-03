<?php

declare(strict_types=1);

namespace Src\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author PSPEPP3R <prosperpepple12@gmail.com>
 */
class TestCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:test')
            ->setDescription('Test command for Prophet');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('Command Successful', true);
        return Command::SUCCESS;
    }
}
