<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain\Handlers;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandContext;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\Abstracts\AbstractCommandHandler;
use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

final class LogResultHandler extends AbstractCommandHandler
{
    public function __construct(
        protected readonly LoggerInterface $logger,
        MessageProviderInterface $messageProvider
    ) {
        parent::__construct($messageProvider);
    }

    protected function process(CommandContext $context): ?CommandResultDto
    {
        if ($context->result === null) {
            return CommandResultDto::error(
                errorMessage: $this->messageProvider->getMessage(key: 'result_not_found')
            );
        }

        $this->logResult($context);

        return $context->result;
    }

    protected function logResult(CommandContext $context): void
    {
        $result = $context->result;

        if ($result === null) {
            return;
        }

        $logMessage = $result->isSuccess()
            ? ($result->getMessage() ?? $this->messageProvider->getMessage(key: 'command_executed_successfully'))
            : ($result->getErrorMessage() ?? $this->messageProvider->getMessage(key: 'command_execution_error'));

        $result->isSuccess()
            ? $this->logger->logCommandExecution(
                command: $context->rawCommand,
                dealId: (string) $context->dealId,
                result: $logMessage
            )
            : $this->logger->logCommandError(
                command: $context->rawCommand,
                dealId: (string) $context->dealId,
                error: $logMessage
            );
    }
}
