<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain\Handlers;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandContext;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\Abstracts\AbstractCommandHandler;
use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

final class ValidateCommandHandler extends AbstractCommandHandler
{
    public function __construct(
        MessageProviderInterface $messageProvider,
        protected readonly LoggerInterface $logger
    ) {
        parent::__construct($messageProvider);
    }

    protected function process(CommandContext $context): ?CommandResultDto
    {
        if ($context->command === null || $context->input === null) {
            return CommandResultDto::error(
                $this->messageProvider->getMessage(key: 'command_not_found_or_recognized_validation')
            );
        }

        if (! $context->command->canExecute($context->input)) {
            $error = $this->messageProvider->getMessage('command_cannot_be_executed');

            $this->logger->logCommandError(
                command: $context->rawCommand,
                dealId: (string) $context->dealId,
                error: $error
            );

            return CommandResultDto::error($error);
        }

        return null;
    }
}
