<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\Interfaces\CommandHandlerInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

final class CommandChainManager
{
    protected CommandHandlerInterface $firstHandler;

    public function __construct(CommandHandlerInterface $firstHandler)
    {
        $this->firstHandler = $firstHandler;
    }

    public function process(string $rawCommand, DealId $dealId): CommandResultDto
    {
        $context = new CommandContext(
            rawCommand: $rawCommand,
            dealId: $dealId
        );

        return $this->firstHandler->handle($context);
    }
}
