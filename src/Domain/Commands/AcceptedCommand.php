<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Commands;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Domain\Commands\Abstracts\AbstractConfigurableCommand;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigurationInterface;
use Avangero\DealCmdPackage\Domain\Ports\DealRepositoryInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyValue;

/**
 * Команда /принято 500 офис - устанавливает свойства сделки согласно конфигурации
 * Конфигурация должна содержать:
 * - 'amount_property' => PropertyId для суммы
 * - 'type_property' => PropertyId для типа
 */
final class AcceptedCommand extends AbstractConfigurableCommand
{
    public function __construct(
        CommandConfigurationInterface $configuration,
        MessageProviderInterface $messageProvider,
        protected DealRepositoryInterface $dealRepository
    ) {
        parent::__construct($configuration, $messageProvider);
    }

    public function getName(): string
    {
        return 'accepted';
    }

    public function execute(CommandInputDto $input): CommandResultDto
    {
        if (! $this->canExecute($input)) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'accepted_command_requires_two_arguments'));
        }

        $config = $this->getConfig();

        if ($config === null) {
            return $this->createConfigError();
        }

        $amountPropertyId = $config->getPropertyId(key: 'amount_property');
        $typePropertyId = $config->getPropertyId(key: 'type_property');

        if ($amountPropertyId === null || $typePropertyId === null) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'accepted_command_incomplete_config'));
        }

        $amount = $input->getArgument(0);
        $type = $input->getArgument(1);

        if ($amount === null || $type === null) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'accepted_command_requires_two_arguments'));
        }

        $this->dealRepository->setProperty(
            dealId: $input->getDealId(),
            propertyId: $amountPropertyId,
            value: new PropertyValue($amount)
        );

        $this->dealRepository->setProperty(
            dealId: $input->getDealId(),
            propertyId: $typePropertyId,
            value: new PropertyValue($type)
        );

        return CommandResultDto::success(
            $this->messageProvider->getMessage(
                key: 'accepted_command_success',
                parameters: [
                    'amount_property' => (string) $amountPropertyId,
                    'amount' => $amount,
                    'type_property' => (string) $typePropertyId,
                    'type' => $type,
                ]
            )
        );
    }

    public function canExecute(CommandInputDto $input): bool
    {
        return $input->getArgumentsCount() >= 2
            && $input->getArgument(0) !== null
            && $input->getArgument(1) !== null;
    }
}
