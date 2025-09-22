<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Application\Services\Chain\Handlers;

use Avangero\DealCmdPackage\Application\DTOs\CommandResultDto;
use Avangero\DealCmdPackage\Application\Services\Chain\CommandContext;
use Avangero\DealCmdPackage\Application\Services\Chain\Handlers\Abstracts\AbstractCommandHandler;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandParserInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use InvalidArgumentException;

final class ParseCommandHandler extends AbstractCommandHandler
{
    public function __construct(
        protected readonly CommandParserInterface $parser,
        MessageProviderInterface $messageProvider
    ) {
        parent::__construct($messageProvider);
    }

    protected function process(CommandContext $context): ?CommandResultDto
    {
        try {
            $input = $this->parser->parse(rawCommand: $context->rawCommand, dealId: $context->dealId);

            $context = $context->withInput($input);

            return $this->nextHandler?->handle($context);
        } catch (InvalidArgumentException $e) {
            return CommandResultDto::error(errorMessage: $e->getMessage());
        }
    }
}
