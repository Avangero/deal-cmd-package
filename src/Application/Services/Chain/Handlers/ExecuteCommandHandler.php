<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain\Handlers;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandContext;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\Abstracts\AbstractCommandHandler;

final class ExecuteCommandHandler extends AbstractCommandHandler
{
    protected function process(CommandContext $context): ?CommandResultDto
    {
        if ($context->command === null || $context->input === null) {
            return CommandResultDto::error(
                errorMessage: $this->messageProvider->getMessage(key: 'command_not_found_or_recognized')
            );
        }

        $result = $context->command->execute(input: $context->input);
        $context = $context->withResult($result);

        return $this->nextHandler?->handle($context);
    }
}
