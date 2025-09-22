<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Services;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandParserInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;
use InvalidArgumentException;

final readonly class CommandParser implements CommandParserInterface
{
    public function __construct(protected MessageProviderInterface $messageProvider) {}

    public function parse(string $rawCommand, DealId $dealId): CommandInputDto
    {
        $trimmed = trim($rawCommand);

        if (! str_starts_with($trimmed, '/')) {
            throw new InvalidArgumentException($this->messageProvider->getMessage(key: 'command_must_start_with_slash'));
        }

        $commandWithoutSlash = substr($trimmed, 1);

        if (empty($commandWithoutSlash)) {
            throw new InvalidArgumentException($this->messageProvider->getMessage(key: 'command_cannot_be_empty'));
        }

        $parts = preg_split('/\s+/', $commandWithoutSlash);

        if (empty($parts)) {
            throw new InvalidArgumentException($this->messageProvider->getMessage(key: 'command_parse_failed'));
        }

        $commandName = array_shift($parts);
        $arguments = $parts;

        return new CommandInputDto(rawCommand: $rawCommand, commandName: $commandName, arguments: $arguments, dealId: $dealId);
    }
}
