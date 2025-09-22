<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Commands\Interfaces;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;

interface CommandInterface
{
    public function getName(): string;

    public function execute(CommandInputDto $input): CommandResultDto;

    public function canExecute(CommandInputDto $input): bool;
}
