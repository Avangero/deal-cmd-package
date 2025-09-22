<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\ValueObjects;

use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use InvalidArgumentException;

final readonly class DealIdFactory
{
    public function __construct(protected MessageProviderInterface $messageProvider) {}

    public function create(int $value): DealId
    {
        if ($value <= 0) {
            throw new InvalidArgumentException($this->messageProvider->getMessage(key: 'deal_id_must_be_positive'));
        }

        return new DealId($value);
    }
}
