<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain;

use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\ExecuteCommandHandler;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\LogResultHandler;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\ParseCommandHandler;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\ResolveCommandHandler;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\ValidateCommandHandler;
use Avangero\DealCmdPackage\Domain\Commands\Interfaces\CommandInterface;
use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandMapperInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandParserInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

final class CommandChainFactory
{
    /**
     * @param  CommandInterface[]  $commands
     */
    public function createChain(
        CommandParserInterface $parser,
        CommandMapperInterface $commandMapper,
        MessageProviderInterface $messageProvider,
        LoggerInterface $logger,
        array $commands
    ): CommandChainManager {
        $parseHandler = new ParseCommandHandler($parser, $messageProvider);
        $resolveHandler = new ResolveCommandHandler($commandMapper, $messageProvider, $logger, $commands);
        $validateHandler = new ValidateCommandHandler($messageProvider, $logger);
        $executeHandler = new ExecuteCommandHandler($messageProvider);
        $logHandler = new LogResultHandler($logger, $messageProvider);

        $parseHandler
            ->setNext($resolveHandler)
            ->setNext($validateHandler)
            ->setNext($executeHandler)
            ->setNext($logHandler);

        return new CommandChainManager($parseHandler);
    }
}
