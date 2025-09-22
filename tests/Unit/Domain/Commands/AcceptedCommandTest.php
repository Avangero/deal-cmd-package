<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Tests\Unit\Domain\Commands;

use Avangero\DealCmdPackage\Application\DTOs\CommandInputDto;
use Avangero\DealCmdPackage\Domain\Commands\AcceptedCommand;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigDto;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigurationInterface;
use Avangero\DealCmdPackage\Domain\Ports\DealRepositoryInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AcceptedCommandTest extends TestCase
{
    protected AcceptedCommand $command;

    protected DealRepositoryInterface&MockObject $dealRepository;

    protected CommandConfigurationInterface&MockObject $configuration;

    protected MessageProviderInterface&MockObject $messageProvider;

    protected function setUp(): void
    {
        $this->dealRepository = $this->createMock(DealRepositoryInterface::class);
        $this->configuration = $this->createMock(CommandConfigurationInterface::class);
        $this->messageProvider = $this->createMock(MessageProviderInterface::class);
        $this->command = new AcceptedCommand(
            $this->configuration,
            $this->messageProvider,
            $this->dealRepository
        );
    }

    /**
     * @test
     */
    public function get_name(): void
    {
        $this->assertEquals('accepted', $this->command->getName());
    }

    /**
     * @test
     */
    public function can_execute_with_valid_arguments(): void
    {
        $input = new CommandInputDto(
            rawCommand: '/принято 500 офис',
            commandName: 'принято',
            arguments: ['500', 'офис'],
            dealId: new DealId(123)
        );

        $this->assertTrue($this->command->canExecute($input));
    }

    /**
     * @test
     */
    public function can_execute_with_insufficient_arguments(): void
    {
        $input = new CommandInputDto(
            rawCommand: '/принято 500',
            commandName: 'принято',
            arguments: ['500'],
            dealId: new DealId(123)
        );

        $this->assertFalse($this->command->canExecute($input));
    }

    /**
     * @test
     */
    public function can_execute_with_no_arguments(): void
    {
        $input = new CommandInputDto(
            rawCommand: '/принято',
            commandName: 'принято',
            arguments: [],
            dealId: new DealId(123)
        );

        $this->assertFalse($this->command->canExecute($input));
    }

    /**
     * @test
     */
    public function execute_success(): void
    {
        $dealId = new DealId(123);
        $input = new CommandInputDto(
            rawCommand: '/принято 500 офис',
            commandName: 'принято',
            arguments: ['500', 'офис'],
            dealId: $dealId
        );

        $config = new CommandConfigDto('accepted', [
            'amount_property' => new PropertyId(14),
            'type_property' => new PropertyId(15),
        ]);

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
            ->method('setProperty')
            ->willReturnCallback(function ($actualDealId, $actualPropertyId, $actualValue) use ($dealId) {
                static $callCount = 0;
                $callCount++;

                if ($callCount === 1) {
                    $this->assertEquals($dealId, $actualDealId);
                    $this->assertEquals(new PropertyId(14), $actualPropertyId);
                    $this->assertEquals(new PropertyValue('500'), $actualValue);
                } elseif ($callCount === 2) {
                    $this->assertEquals($dealId, $actualDealId);
                    $this->assertEquals(new PropertyId(15), $actualPropertyId);
                    $this->assertEquals(new PropertyValue('офис'), $actualValue);
                }
            });

        $result = $this->command->execute($input);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Установлены свойства: #14 = 500, #15 = офис', $result->getMessage());
        $this->assertNull($result->getErrorMessage());
    }

    /**
     * @test
     */
    public function execute_with_insufficient_arguments(): void
    {
        $input = new CommandInputDto(
            rawCommand: '/принято 500',
            commandName: 'принято',
            arguments: ['500'],
            dealId: new DealId(123)
        );

        $this->messageProvider
            ->expects($this->once())
            ->method('getMessage')
            ->with('accepted_command_requires_two_arguments')
            ->willReturn('Команда "принято" требует два аргумента: сумму и тип');

        $this->configuration
            ->expects($this->never())
            ->method('getCommandConfig');

        $this->dealRepository
            ->expects($this->never())
            ->method('setProperty');

        $result = $this->command->execute($input);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Команда "принято" требует два аргумента: сумму и тип', $result->getErrorMessage());
        $this->assertNull($result->getMessage());
    }

    /**
     * @test
     */
    public function execute_with_missing_configuration(): void
    {
        $input = new CommandInputDto(
            rawCommand: '/принято 500 офис',
            commandName: 'принято',
            arguments: ['500', 'офис'],
            dealId: new DealId(123)
        );

        $this->configuration
            ->expects($this->once())
            ->method('getCommandConfig')
            ->with('accepted')
            ->willReturn(null);

        $this->messageProvider
            ->expects($this->once())
            ->method('getMessage')
            ->with('configuration_not_found', ['command_name' => 'accepted'])
            ->willReturn('Конфигурация для команды \'accepted\' не найдена');

        $result = $this->command->execute($input);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Конфигурация для команды \'accepted\' не найдена', $result->getErrorMessage());
    }
}
