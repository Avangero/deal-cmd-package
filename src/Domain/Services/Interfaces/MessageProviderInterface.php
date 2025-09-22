<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Services\Interfaces;

use Stringable;

interface MessageProviderInterface
{
    /**
     * @param  array<string, scalar|Stringable>  $parameters
     */
    public function getMessage(string $key, array $parameters = []): string;

    public function hasMessage(string $key): bool;
}
