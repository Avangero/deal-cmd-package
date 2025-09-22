<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Tests\Unit\Domain\Services;

use Avangero\DealCmdPackage\Domain\Services\CommandParser;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CommandParserTest extends TestCase
{
    protected CommandParser $parser;

    protected MessageProviderInterface&MockObject $messageProvider;

    protected function setUp(): void
    {
        $this->messageProvider = $this->createMock(MessageProviderInterface::class);
        $this->parser = new CommandParser($this->messageProvider);

        $this->messageProvider
            ->method('getMessage')
            ->willReturnCallback(function (string $key) {
                return match ($key) {
                    'command_must_start_with_slash' => 'Команда должна начинаться с символа "/"',
                    'command_cannot_be_empty' => 'Команда не может быть пустой',
                    'command_parse_failed' => 'Не удалось разобрать команду',
                    default => $key,
                };
            });
    }

    /**
     * @test
     */
    public function parse_valid_command(): void
    {
        $dealId = new DealId(123);
        $result = $this->parser->parse('/принято 500 офис', $dealId);

        $this->assertEquals('/принято 500 офис', $result->getRawCommand());
        $this->assertEquals('принято', $result->getCommandName());
        $this->assertEquals(['500', 'офис'], $result->getArguments());
        $this->assertEquals($dealId, $result->getDealId());
        $this->assertEquals(2, $result->getArgumentsCount());
    }

    /**
     * @test
     */
    public function parse_command_without_arguments(): void
    {
        $dealId = new DealId(123);
        $result = $this->parser->parse('/контакт', $dealId);

        $this->assertEquals('/контакт', $result->getRawCommand());
        $this->assertEquals('контакт', $result->getCommandName());
        $this->assertEquals([], $result->getArguments());
        $this->assertEquals($dealId, $result->getDealId());
        $this->assertEquals(0, $result->getArgumentsCount());
    }

    /**
     * @test
     */
    public function parse_command_with_multiple_spaces(): void
    {
        $dealId = new DealId(123);
        $result = $this->parser->parse('  /причина_закрытия   удалена   транзакция  ', $dealId);

        $this->assertEquals('причина_закрытия', $result->getCommandName());
        $this->assertEquals(['удалена', 'транзакция'], $result->getArguments());
    }

    /**
     * @test
     */
    public function parse_throws_exception_when_command_does_not_start_with_slash(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Команда должна начинаться с символа "/"');

        $dealId = new DealId(123);
        $this->parser->parse('принято 500', $dealId);
    }

    /**
     * @test
     */
    public function parse_throws_exception_when_command_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Команда не может быть пустой');

        $dealId = new DealId(123);
        $this->parser->parse('/', $dealId);
    }

    /**
     * @test
     */
    public function parse_throws_exception_when_command_is_only_spaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Команда не может быть пустой');

        $dealId = new DealId(123);
        $this->parser->parse('/   ', $dealId);
    }

    /**
     * @test
     */
    public function get_argument(): void
    {
        $dealId = new DealId(123);
        $result = $this->parser->parse('/принято 500 офис', $dealId);

        $this->assertEquals('500', $result->getArgument(0));
        $this->assertEquals('офис', $result->getArgument(1));
        $this->assertNull($result->getArgument(2));
    }

    /**
     * @test
     */
    public function has_argument(): void
    {
        $dealId = new DealId(123);
        $result = $this->parser->parse('/принято 500 офис', $dealId);

        $this->assertTrue($result->hasArgument(0));
        $this->assertTrue($result->hasArgument(1));
        $this->assertFalse($result->hasArgument(2));
    }
}
