<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Domain\Configuration;

interface CommandConfigurationInterface
{
    public function getCommandConfig(string $commandName): ?CommandConfigDto;
}
