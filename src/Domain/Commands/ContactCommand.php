<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Commands;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Domain\Commands\Interfaces\CommandInterface;
use Avangero\DealCmdPackage\Domain\Ports\DealRepositoryInterface;
use Avangero\DealCmdPackage\Domain\Ports\MessageSenderInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;

/**
 * Команда /контакт - выводит служебное сообщение в сделку с текстом, содержащим контакт клиента
 * Эта команда не требует конфигурации, так как работает с методом getClientContact
 */
final readonly class ContactCommand implements CommandInterface
{
    public function __construct(
        protected MessageProviderInterface $messageProvider,
        protected DealRepositoryInterface $dealRepository,
        protected MessageSenderInterface $messageSender
    ) {}

    public function getName(): string
    {
        return 'contact';
    }

    public function execute(CommandInputDto $input): CommandResultDto
    {
        $contact = $this->dealRepository->getClientContact(dealId: $input->getDealId());

        if ($contact === null) {
            return CommandResultDto::error($this->messageProvider->getMessage(key: 'contact_command_client_not_found'));
        }

        $message = $this->messageProvider->getMessage(
            key: 'contact_message_template',
            parameters: ['contact' => $contact]
        );

        $this->messageSender->sendServiceMessage(dealId: $input->getDealId(), message: $message);

        return CommandResultDto::success($this->messageProvider->getMessage(key: 'contact_command_success'));
    }

    public function canExecute(CommandInputDto $input): bool
    {
        return true;
    }
}
