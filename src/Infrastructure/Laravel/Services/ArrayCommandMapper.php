<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Infrastructure\Laravel\Services;

use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandMapperInterface;

final readonly class ArrayCommandMapper implements CommandMapperInterface
{
    /**
     * @param  array<string, string>  $mapping  Маппинг пользовательских команд к системным
     */
    public function __construct(protected array $mapping) {}

    public function mapUserCommandToSystem(string $userCommand): ?string
    {
        return $this->mapping[$userCommand] ?? null;
    }

    /**
     * @return array<int, string>
     */
    public function getAvailableUserCommands(): array
    {
        return array_keys($this->mapping);
    }
}
