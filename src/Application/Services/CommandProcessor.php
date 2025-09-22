<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandChainFactory;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandChainManager;
use Avangero\DealCmdPackage\Domain\Commands\Interfaces\CommandInterface;
use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandMapperInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandParserInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

final class CommandProcessor
{
    /**
     * @param  CommandInterface[]  $commands
     */
    public function __construct(
        protected CommandParserInterface $parser,
        protected LoggerInterface $logger,
        protected MessageProviderInterface $messageProvider,
        protected CommandMapperInterface $commandMapper,
        protected array $commands
    ) {}

    public function process(string $rawCommand, DealId $dealId): CommandResultDto
    {
        $chainManager = $this->createChainManager();

        return $chainManager->process($rawCommand, $dealId);
    }

    protected function createChainManager(): CommandChainManager
    {
        $factory = new CommandChainFactory;

        return $factory->createChain(
            parser: $this->parser,
            commandMapper: $this->commandMapper,
            messageProvider: $this->messageProvider,
            logger: $this->logger,
            commands: $this->commands
        );
    }
}
