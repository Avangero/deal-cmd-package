<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Infrastructure\Laravel\Adapters;

use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

final readonly class LaravelLoggerAdapter implements LoggerInterface
{
    public function __construct(protected PsrLoggerInterface $logger) {}

    public function logCommandExecution(string $command, string $dealId, string $result): void
    {
        $this->logger->info('Command executed', [
            'command' => $command,
            'deal_id' => $dealId,
            'result' => $result,
        ]);
    }

    public function logCommandError(string $command, string $dealId, string $error): void
    {
        $this->logger->error('Command execution failed', [
            'command' => $command,
            'deal_id' => $dealId,
            'error' => $error,
        ]);
    }
}
