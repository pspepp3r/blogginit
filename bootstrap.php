<?php
declare(strict_types=1);

use Dotenv\Dotenv;

require 'vendor/autoload.php';
require 'configs/path_constants.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

return require CONFIG_PATH . '/container/container.php';
