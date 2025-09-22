<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain\Handlers;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandContext;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\Abstracts\AbstractCommandHandler;
use Avangero\DealCmdPackage\Domain\Commands\Interfaces\CommandInterface;
use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandMapperInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

final class ResolveCommandHandler extends AbstractCommandHandler
{
    /**
     * @param  CommandInterface[]  $commands
     */
    public function __construct(
        protected readonly CommandMapperInterface $commandMapper,
        MessageProviderInterface $messageProvider,
        protected readonly LoggerInterface $logger,
        protected readonly array $commands
    ) {
        parent::__construct($messageProvider);
    }

    protected function process(CommandContext $context): ?CommandResultDto
    {
        if ($context->input === null) {
            return CommandResultDto::error(
                errorMessage: $this->messageProvider->getMessage(key: 'command_not_recognized')
            );
        }

        $command = $this->findCommand(input: $context->input);

        if ($command === null) {
            $error = $this->messageProvider->getMessage(
                key: 'unknown_command',
                parameters: ['command_name' => $context->input->getCommandName()]
            );

            $this->logger->logCommandError(
                command: $context->rawCommand,
                dealId: (string) $context->dealId,
                error: $error
            );

            return CommandResultDto::error(errorMessage: $error);
        }

        $context = $context->withCommand($command);

        return $this->nextHandler?->handle($context);
    }

    protected function findCommand(CommandInputDto $input): ?CommandInterface
    {
        $systemCommandName = $this->commandMapper->mapUserCommandToSystem(userCommand: $input->getCommandName());

        if ($systemCommandName === null) {
            return null;
        }

        return array_find($this->commands, fn ($command) => $command->getName() === $systemCommandName);
    }
}
