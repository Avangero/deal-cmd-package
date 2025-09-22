<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain\Handlers\Abstracts;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandContext;
use Avangero\DealCmdPackage\Application\Services\Chain\Interfaces\CommandHandlerInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

abstract class AbstractCommandHandler implements CommandHandlerInterface
{
    protected ?CommandHandlerInterface $nextHandler = null;

    public function __construct(protected readonly MessageProviderInterface $messageProvider) {}

    public function setNext(CommandHandlerInterface $handler): CommandHandlerInterface
    {
        $this->nextHandler = $handler;

        return $handler;
    }

    public function handle(CommandContext $context): CommandResultDto
    {
        $result = $this->process($context);

        if ($result !== null) {
            return $result;
        }

        if ($this->nextHandler !== null) {
            return $this->nextHandler->handle($context);
        }

        return CommandResultDto::error(
            $this->messageProvider->getMessage(key: 'command_processing_failed')
        );
    }

    abstract protected function process(CommandContext $context): ?CommandResultDto;
}
