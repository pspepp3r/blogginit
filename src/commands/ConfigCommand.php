<?php

declare(strict_types=1);

namespace Src\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author PSPEPP3R <prosperpepple12@gmail.com>
 */
class ConfigCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:config:set')
            ->setDescription('Changes or adds a new config option to the configs/app file.')
            ->addArgument('config-key', InputArgument::REQUIRED, 'The config key.')
            ->addArgument('config-value', InputArgument::REQUIRED, 'The config value.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $configFilePath = CONFIG_PATH . '/app.php';
        $configKey = $input->getArgument('config-key');
        $configValue = $input->getArgument('config-value');

        $pattern = "/^$configKey.*/m";

        $configFileContent = file_get_contents($configFilePath);

        if (!file_exists($configFilePath)) {
            throw new \RuntimeException('File: "configs/app.php" not found');
        }

        if (preg_match($pattern, $configKey)) {
            $configFileContent = preg_replace($pattern, "$configKey=>$configValue", $configFileContent);
            file_put_contents($configFilePath, $configFileContent);
        } else {
            $configFileContent .= PHP_EOL . "'$configKey' => '$configValue'";
        }

        $output->write("$configKey has been added to config file with value $configValue", true);

        return Command::SUCCESS;
    }
}
