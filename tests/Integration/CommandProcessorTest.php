<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Tests\Integration;

use Avangero\DealCmdPackage\Application\Services\CommandProcessor;
use Avangero\DealCmdPackage\Domain\Commands\AcceptedCommand;
use Avangero\DealCmdPackage\Domain\Commands\ContactCommand;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigDto;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigurationInterface;
use Avangero\DealCmdPackage\Domain\Ports\DealRepositoryInterface;
use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Avangero\DealCmdPackage\Domain\Ports\MessageSenderInterface;
use Avangero\DealCmdPackage\Domain\Services\CommandParser;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandMapperInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CommandProcessorTest extends TestCase
{
    protected CommandProcessor $processor;

    protected DealRepositoryInterface&MockObject $dealRepository;

    protected LoggerInterface&MockObject $logger;

    protected MessageSenderInterface&MockObject $messageSender;

    protected CommandConfigurationInterface&MockObject $configuration;

    protected MessageProviderInterface&MockObject $messageProvider;

    protected CommandMapperInterface&MockObject $commandMapper;

    protected function setUp(): void
    {
        $this->dealRepository = $this->createMock(DealRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->messageSender = $this->createMock(MessageSenderInterface::class);
        $this->configuration = $this->createMock(CommandConfigurationInterface::class);
        $this->messageProvider = $this->createMock(MessageProviderInterface::class);
        $this->commandMapper = $this->createMock(CommandMapperInterface::class);

        $this->messageProvider
            ->method('getMessage')
            ->willReturnCallback(function (string $key, array $parameters = []) {
                $messages = [
                    'command_must_start_with_slash' => 'Команда должна начинаться с символа "/"',
                    'command_cannot_be_empty' => 'Команда не может быть пустой',
                    'command_parse_failed' => 'Не удалось разобрать команду',
                    'unknown_command' => 'Неизвестная команда: {command_name}',
                    'command_cannot_be_executed' => 'Команда не может быть выполнена с данными аргументами',
                    'accepted_command_success' => 'Установлены свойства: #{amount_property} = {amount}, #{type_property} = {type}',
                ];

                $message = $messages[$key] ?? $key;

                foreach ($parameters as $paramKey => $paramValue) {
                    $message = str_replace('{' . $paramKey . '}', $paramValue, $message);
                }

                return $message;
            });

        $commands = [
            new AcceptedCommand($this->configuration, $this->messageProvider, $this->dealRepository),
            new ContactCommand($this->messageProvider, $this->dealRepository, $this->messageSender),
        ];

        $this->processor = new CommandProcessor(
            new CommandParser($this->messageProvider),
            $this->logger,
            $this->messageProvider,
            $this->commandMapper,
            $commands
        );
    }

    /**
     * @test
     */
    public function process_valid_command(): void
    {
        $dealId = new DealId(123);

        $config = new CommandConfigDto('accepted', [
            'amount_property' => new PropertyId(14),
            'type_property' => new PropertyId(15),
        ]);

        $this->commandMapper
            ->expects($this->once())
            ->method('mapUserCommandToSystem')
            ->with('принято')
            ->willReturn('accepted');

        $this->configuration
            ->expects($this->once())
            ->method('getCommandConfig')
            ->with('accepted')
            ->willReturn($config);

        $this->messageProvider
            ->expects($this->once())
            ->method('getMessage')
            ->with('accepted_command_success', [
                'amount_property' => '14',
                'amount' => '500',
                'type_property' => '15',
                'type' => 'офис',
            ])
            ->willReturn('Установлены свойства: #14 = 500, #15 = офис');

        $this->dealRepository
            ->expects($this->exactly(2))
            ->method('setProperty');

        $this->logger
            ->expects($this->once())
            ->method('logCommandExecution')
            ->with('/принято 500 офис', '123', 'Установлены свойства: #14 = 500, #15 = офис');

        $result = $this->processor->process('/принято 500 офис', $dealId);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Установлены свойства: #14 = 500, #15 = офис', $result->getMessage());
    }

    /**
     * @test
     */
    public function process_unknown_command(): void
    {
        $dealId = new DealId(123);

        $this->commandMapper
            ->expects($this->once())
            ->method('mapUserCommandToSystem')
            ->with('неизвестная')
            ->willReturn(null);

        $this->messageProvider
            ->expects($this->once())
            ->method('getMessage')
            ->with('unknown_command', ['command_name' => 'неизвестная'])
            ->willReturn('Неизвестная команда: неизвестная');

        $this->logger
            ->expects($this->once())
            ->method('logCommandError')
            ->with('/неизвестная команда', '123', 'Неизвестная команда: неизвестная');

        $result = $this->processor->process('/неизвестная команда', $dealId);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Неизвестная команда: неизвестная', $result->getErrorMessage());
    }

    /**
     * @test
     */
    public function process_invalid_command_format(): void
    {
        $dealId = new DealId(123);

        $result = $this->processor->process('принято 500', $dealId);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Команда должна начинаться с символа "/"', $result->getErrorMessage());
    }

    /**
     * @test
     */
    public function process_command_that_cannot_be_executed(): void
    {
        $dealId = new DealId(123);

        $this->commandMapper
            ->expects($this->once())
            ->method('mapUserCommandToSystem')
            ->with('принято')
            ->willReturn('accepted');

        $this->messageProvider
            ->expects($this->once())
            ->method('getMessage')
            ->with('command_cannot_be_executed')
            ->willReturn('Команда не может быть выполнена с данными аргументами');

        $this->logger
            ->expects($this->once())
            ->method('logCommandError')
            ->with('/принято 500', '123', 'Команда не может быть выполнена с данными аргументами');

        $result = $this->processor->process('/принято 500', $dealId);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Команда не может быть выполнена с данными аргументами', $result->getErrorMessage());
    }
}
