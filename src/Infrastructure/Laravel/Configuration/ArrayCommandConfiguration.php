<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Infrastructure\Laravel\Configuration;

use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigDto;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigurationInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyIdFactory;

/**
 * Конфигурация команд на основе массива
 * Позволяет настроить все ID свойств извне пакета
 */
final readonly class ArrayCommandConfiguration implements CommandConfigurationInterface
{
    /**
     * @param  array<string, array<string, mixed>>  $configuration
     */
    public function __construct(
        protected array $configuration,
        protected PropertyIdFactory $propertyIdFactory
    ) {}

    public function getCommandConfig(string $commandName): ?CommandConfigDto
    {
        $config = $this->configuration[$commandName] ?? null;

        if ($config === null) {
            return null;
        }

        $propertyIds = [];
        $additionalParameters = [];

        foreach ($config as $key => $value) {
            if (str_ends_with($key, '_property') && is_int($value)) {
                $propertyIds[$key] = $this->propertyIdFactory->create($value);
            } else {
                $additionalParameters[$key] = (string) $value;
            }
        }

        return new CommandConfigDto($commandName, $propertyIds, $additionalParameters);
    }
}
