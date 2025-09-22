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
 * Команда /причина_закрытия удалена транзакция - устанавливает свойство сделки согласно конфигурации
 * Конфигурация должна содержать:
 * - 'close_reason_property' => PropertyId для причины закрытия
 */
final class CloseReasonCommand extends AbstractConfigurableCommand
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
        return 'close_reason';
    }

    public function execute(CommandInputDto $input): CommandResultDto
    {
        if (! $this->canExecute($input)) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'close_reason_command_requires_reason'));
        }

        $config = $this->getConfig();

        if ($config === null) {
            return $this->createConfigError();
        }

        $closeReasonPropertyId = $config->getPropertyId(key: 'close_reason_property');

        if ($closeReasonPropertyId === null) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'close_reason_command_incomplete_config'));
        }

        $reason = implode(' ', $input->getArguments());

        $this->dealRepository->setProperty(
            dealId: $input->getDealId(),
            propertyId: $closeReasonPropertyId,
            value: new PropertyValue($reason)
        );

        return CommandResultDto::success(
            $this->messageProvider->getMessage(
                key: 'close_reason_command_success',
                parameters: ['reason' => $reason]
            )
        );
    }

    public function canExecute(CommandInputDto $input): bool
    {
        return $input->getArgumentsCount() > 0;
    }
}
