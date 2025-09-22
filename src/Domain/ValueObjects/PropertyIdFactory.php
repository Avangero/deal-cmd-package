<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\ValueObjects;

use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use InvalidArgumentException;

final readonly class PropertyIdFactory
{
    public function __construct(protected MessageProviderInterface $messageProvider) {}

    public function create(int $value): PropertyId
    {
        if ($value <= 0) {
            throw new InvalidArgumentException($this->messageProvider->getMessage(key: 'property_id_must_be_positive'));
        }

        return new PropertyId($value);
    }
}
