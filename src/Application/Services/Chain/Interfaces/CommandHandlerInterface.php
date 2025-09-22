<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain\Interfaces;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandContext;

interface CommandHandlerInterface
{
    public function handle(CommandContext $context): CommandResultDto;

    public function setNext(CommandHandlerInterface $handler): CommandHandlerInterface;
}
