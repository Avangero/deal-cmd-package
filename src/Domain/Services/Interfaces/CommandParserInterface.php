<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Services\Interfaces;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

interface CommandParserInterface
{
    /**
     * Парсит текстовую команду в DTO
     */
    public function parse(string $rawCommand, DealId $dealId): CommandInputDto;
}
