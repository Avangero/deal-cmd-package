<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Services\Interfaces;

interface CommandMapperInterface
{
    public function mapUserCommandToSystem(string $userCommand): ?string;

    /**
     * @return array<int, string>
     */
    public function getAvailableUserCommands(): array;
}
