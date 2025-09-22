<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Infrastructure\Laravel\Services;

use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Stringable;

final readonly class ArrayMessageProvider implements MessageProviderInterface
{
    /**
     * @param  array<string, string>  $messages
     */
    public function __construct(protected array $messages) {}

    /**
     * @param  array<string, scalar|Stringable>  $parameters
     */
    public function getMessage(string $key, array $parameters = []): string
    {
        $message = $this->messages[$key] ?? $key;

        foreach ($parameters as $param => $value) {
            $message = str_replace("{{$param}}", (string) $value, $message);
        }

        return $message;
    }

    public function hasMessage(string $key): bool
    {
        return isset($this->messages[$key]);
    }
}
