<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Domain\Commands\Interfaces\CommandInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

final class CommandContext
{
    public function __construct(
        public string $rawCommand,
        public DealId $dealId,
        public ?CommandInputDto $input = null,
        public ?CommandInterface $command = null,
        public ?CommandResultDto $result = null,
        public ?string $error = null
    ) {}

    public function withInput(CommandInputDto $input): self
    {
        return new self(
            rawCommand: $this->rawCommand,
            dealId: $this->dealId,
            input: $input,
            command: $this->command,
            result: $this->result,
            error: $this->error
        );
    }

    public function withCommand(CommandInterface $command): self
    {
        return new self(
            rawCommand: $this->rawCommand,
            dealId: $this->dealId,
            input: $this->input,
            command: $command,
            result: $this->result,
            error: $this->error
        );
    }

    public function withResult(CommandResultDto $result): self
    {
        return new self(
            rawCommand: $this->rawCommand,
            dealId: $this->dealId,
            input: $this->input,
            command: $this->command,
            result: $result,
            error: $this->error
        );
    }
}
