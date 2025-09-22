<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Infrastructure\Laravel\Examples;

use Avangero\DealCmdPackage\Domain\Ports\MessageSenderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

/**
 * Пример реализации отправителя сообщений для Laravel
 * Это только пример - в реальном приложении нужно реализовать отправку сообщений
 */
final class ExampleMessageSender implements MessageSenderInterface
{
    public function sendServiceMessage(DealId $dealId, string $message): void
    {
        // Пример реализации - в реальном приложении здесь будет отправка сообщения
        // Например, создание записи в таблице сообщений:
        // DealMessage::create([
        //     'deal_id' => $dealId->getValue(),
        //     'message' => $message,
        //     'type' => 'service',
        //     'created_at' => now(),
        // ]);

        // Или отправка через событие:
        // event(new ServiceMessageSent($dealId->getValue(), $message));

        // Или отправка через очередь:
        // dispatch(new SendServiceMessage($dealId->getValue(), $message));
    }
}
