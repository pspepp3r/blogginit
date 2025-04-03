<?php

declare(strict_types=1);

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(CONFIG_PATH . '/container/container_bindings.php');
return $containerBuilder->build();
