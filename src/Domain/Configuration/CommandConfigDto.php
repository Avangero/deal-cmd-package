<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Configuration;

use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyId;

final readonly class CommandConfigDto
{
    /**
     * @param  PropertyId[]  $propertyIds
     * @param  string[]  $additionalParameters
     */
    public function __construct(
        protected string $commandName,
        protected array $propertyIds = [],
        protected array $additionalParameters = []
    ) {}

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    /**
     * @return PropertyId[]
     */
    public function getPropertyIds(): array
    {
        return $this->propertyIds;
    }

    public function getPropertyId(string $key): ?PropertyId
    {
        return $this->propertyIds[$key] ?? null;
    }

    /**
     * @return string[]
     */
    public function getAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }

    public function getAdditionalParameter(string $key): ?string
    {
        return $this->additionalParameters[$key] ?? null;
    }
}
