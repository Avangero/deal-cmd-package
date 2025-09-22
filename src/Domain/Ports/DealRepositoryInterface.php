<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Ports;

use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyValue;

interface DealRepositoryInterface
{
    public function setProperty(DealId $dealId, PropertyId $propertyId, PropertyValue $value): void;

    public function getProperty(DealId $dealId, PropertyId $propertyId): ?PropertyValue;

    public function getClientContact(DealId $dealId): ?string;
}
