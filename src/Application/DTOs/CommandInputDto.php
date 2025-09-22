<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\DTOs;

use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

final readonly class CommandInputDto
{
    /**
     * @param  string[]  $arguments
     */
    public function __construct(
        protected string $rawCommand,
        protected string $commandName,
        protected array $arguments,
        protected DealId $dealId
    ) {}

    public function getRawCommand(): string
    {
        return $this->rawCommand;
    }

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getDealId(): DealId
    {
        return $this->dealId;
    }

    public function getArgument(int $index): ?string
    {
        return $this->arguments[$index] ?? null;
    }

    public function hasArgument(int $index): bool
    {
        return isset($this->arguments[$index]);
    }

    public function getArgumentsCount(): int
    {
        return count($this->arguments);
    }
}
