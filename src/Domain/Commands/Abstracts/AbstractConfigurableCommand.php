<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Commands\Abstracts;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Domain\Commands\Interfaces\CommandInterface;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigDto;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigurationInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

abstract class AbstractConfigurableCommand implements CommandInterface
{
    public function __construct(
        protected readonly CommandConfigurationInterface $configuration,
        protected readonly MessageProviderInterface $messageProvider
    ) {}

    protected function getConfig(): ?CommandConfigDto
    {
        return $this->configuration->getCommandConfig(commandName: $this->getName());
    }

    protected function createConfigError(): CommandResultDto
    {
        return CommandResultDto::error(
            $this->messageProvider->getMessage(
                key: 'configuration_not_found',
                parameters: ['command_name' => $this->getName()]
            )
        );
    }
}
