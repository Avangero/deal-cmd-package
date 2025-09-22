<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Ports;

interface LoggerInterface
{
    public function logCommandExecution(string $command, string $dealId, string $result): void;

    public function logCommandError(string $command, string $dealId, string $error): void;
}
