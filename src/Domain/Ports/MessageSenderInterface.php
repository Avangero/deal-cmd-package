<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Ports;

use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

interface MessageSenderInterface
{
    public function sendServiceMessage(DealId $dealId, string $message): void;
}
