<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\DTOs;

final readonly class CommandResultDto
{
    public function __construct(
        protected bool $success,
        protected ?string $message = null,
        protected ?string $errorMessage = null
    ) {}

    public static function success(?string $message = null): self
    {
        return new self(true, $message);
    }

    public static function error(string $errorMessage): self
    {
        return new self(false, null, $errorMessage);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
