<?php

declare(strict_types=1);

namespace Src\Commands;

use Src\Services\ConfigService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author PSPEPP3R <prosperpepple12@gmail.com>
 */
class ChangeProjectNameCommand extends Command
{
    public function __construct(private ConfigService $config)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:change-project-name')
            ->setDescription('Changes the name of the application')
            ->addArgument('new-project-name', InputArgument::REQUIRED, 'The new project name.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $envFilePath = __DIR__ . '/../../.env';
        $consoleFilePath = __DIR__ . '/../../';
        $projectName = $input->getArgument('new-project-name');

        $envFileContent = file_get_contents($envFilePath);

        $pattern = '/^APP_NAME=.*/m';

        if ($projectName === null) {
            throw new \RuntimeException("Argument 'new-project-name' is required in order to execute this command correctly.");
        }

        if (!file_exists($envFilePath)) {
            throw new \RuntimeException('.env file not found');
        } else if (!file_exists($consoleFilePath . strtolower($this->config->get('app.app_name')))) {
            throw new \RuntimeException("Console Configuration file $consoleFilePath not found");
        }

        if (preg_match($pattern, $envFileContent)) {
            $envFileContent = preg_replace($pattern, "APP_NAME=$projectName", $envFileContent);
            file_put_contents($envFilePath, $envFileContent);
        } else {
            $envFileContent .= PHP_EOL . 'APP_NAME=' . $projectName;
        }

        $consoleFile = $consoleFilePath . $this->config->get('app.app_name');
        copy($consoleFile, (string) $consoleFilePath . strtolower($projectName));
        if ($consoleFile !== "$consoleFilePath$projectName")
            unlink($consoleFile);

        $output->write("Project name changed to $projectName, please call all commands as `php " . strtolower($projectName) . " <command>` now!", true);

        return Command::SUCCESS;
    }
}
