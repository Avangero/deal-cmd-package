<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\ValueObjects;

final readonly class PropertyValue
{
    public function __construct(
        protected string $value
    ) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isEmpty(): bool
    {
        return trim($this->value) === '';
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
