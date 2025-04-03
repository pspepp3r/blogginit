<?php

declare(strict_types=1);

use Doctrine\DBAL\Tools\Console as DBALConsole;
use Doctrine\ORM\Tools\Console\Command as Command;

return fn($entityManagerProvider, $connectionProvider): array => [

    // DBAL Commands
    new DBALConsole\Command\RunSqlCommand($connectionProvider),

    // ORM Commands
    new Command\ClearCache\CollectionRegionCommand($entityManagerProvider),
    new Command\ClearCache\EntityRegionCommand($entityManagerProvider),
    new Command\ClearCache\MetadataCommand($entityManagerProvider),
    new Command\ClearCache\QueryCommand($entityManagerProvider),
    new Command\ClearCache\QueryRegionCommand($entityManagerProvider),
    new Command\ClearCache\ResultCommand($entityManagerProvider),
    new Command\SchemaTool\CreateCommand($entityManagerProvider),
    new Command\SchemaTool\UpdateCommand($entityManagerProvider),
    new Command\SchemaTool\DropCommand($entityManagerProvider),
    new Command\GenerateProxiesCommand($entityManagerProvider),
    new Command\RunDqlCommand($entityManagerProvider),
    new Command\ValidateSchemaCommand($entityManagerProvider),
    new Command\InfoCommand($entityManagerProvider),
    new Command\MappingDescribeCommand($entityManagerProvider),

];
