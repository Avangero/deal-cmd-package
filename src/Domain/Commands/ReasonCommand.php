<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Commands;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Domain\Commands\Abstracts\AbstractConfigurableCommand;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigurationInterface;
use Avangero\DealCmdPackage\Domain\Ports\DealRepositoryInterface;
use Avangero\DealCmdPackage\Domain\Ports\MessageSenderInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

/**
 * Команда /причина - выводит служебное сообщение в сделку с текстом, содержащим свойство согласно конфигурации
 * Конфигурация должна содержать:
 * - 'close_reason_property' => PropertyId для причины закрытия
 */
final class ReasonCommand extends AbstractConfigurableCommand
{
    public function __construct(
        CommandConfigurationInterface $configuration,
        MessageProviderInterface $messageProvider,
        protected readonly DealRepositoryInterface $dealRepository,
        protected readonly MessageSenderInterface $messageSender
    ) {
        parent::__construct($configuration, $messageProvider);
    }

    public function getName(): string
    {
        return 'reason';
    }

    public function execute(CommandInputDto $input): CommandResultDto
    {
        $config = $this->getConfig();

        if ($config === null) {
            return $this->createConfigError();
        }

        $closeReasonPropertyId = $config->getPropertyId(key: 'close_reason_property');

        if ($closeReasonPropertyId === null) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'reason_command_incomplete_config'));
        }

        $reason = $this->dealRepository->getProperty(
            dealId: $input->getDealId(),
            propertyId: $closeReasonPropertyId
        );

        if ($reason === null || $reason->isEmpty()) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'reason_command_not_set'));
        }

        $message = $this->messageProvider->getMessage(
            key: 'reason_message_template',
            parameters: ['reason' => $reason->getValue()]
        );

        $this->messageSender->sendServiceMessage(dealId: $input->getDealId(), message: $message);

        return CommandResultDto::success($this->messageProvider->getMessage(key: 'reason_command_success'));
    }

    public function canExecute(CommandInputDto $input): bool
    {
        return true;
    }
}
