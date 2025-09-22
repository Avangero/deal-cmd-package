<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\ValueObjects;

final readonly class DealId
{
    public function __construct(protected int $value) {}

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
