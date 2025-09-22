<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Infrastructure\Laravel\Examples;

use Avangero\DealCmdPackage\Domain\Ports\DealRepositoryInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyValue;

/**
 * Пример реализации репозитория сделок для Laravel
 * Это только пример - в реальном приложении нужно реализовать работу с БД
 */
final class ExampleDealRepository implements DealRepositoryInterface
{
    public function setProperty(DealId $dealId, PropertyId $propertyId, PropertyValue $value): void
    {
        // Пример реализации - в реальном приложении здесь будет работа с БД
        // Например, через Eloquent модель:
        // Deal::where('id', $dealId->getValue())
        //     ->update(["property_{$propertyId->getValue()}" => $value->getValue()]);

        // Или через специальную таблицу свойств:
        // DealProperty::updateOrCreate([
        //     'deal_id' => $dealId->getValue(),
        //     'property_id' => $propertyId->getValue(),
        // ], [
        //     'value' => $value->getValue(),
        // ]);
    }

    public function getProperty(DealId $dealId, PropertyId $propertyId): ?PropertyValue
    {
        // Пример реализации - в реальном приложении здесь будет работа с БД
        // $property = DealProperty::where('deal_id', $dealId->getValue())
        //     ->where('property_id', $propertyId->getValue())
        //     ->first();
        //
        // return $property ? new PropertyValue($property->value) : null;

        return null;
    }

    public function getClientContact(DealId $dealId): ?string
    {
        // Пример реализации - в реальном приложении здесь будет работа с БД
        // $deal = Deal::with('client')->find($dealId->getValue());
        // return $deal?->client?->contact_info;

        return null;
    }
}
