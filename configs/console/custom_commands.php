<?php

declare(strict_types=1);

use Src\Commands\ChangeProjectNameCommand;
use Src\Commands\ConfigCommand;
use Src\Commands\GenerateAppKeyCommand;
use Src\Commands\TestCommand;

return [

    ChangeProjectNameCommand::class,
    ConfigCommand::class,
    GenerateAppKeyCommand::class,
    TestCommand::class,

];
