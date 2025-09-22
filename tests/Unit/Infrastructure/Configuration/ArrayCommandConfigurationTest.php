<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Tests\Unit\Infrastructure\Configuration;

use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigDto;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyId;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyIdFactory;
use Avangero\DealCmdPackage\Infrastructure\Laravel\Configuration\ArrayCommandConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ArrayCommandConfigurationTest extends TestCase
{
    protected MessageProviderInterface&MockObject $messageProvider;
    protected PropertyIdFactory $propertyIdFactory;

    protected function setUp(): void
    {
        $this->messageProvider = $this->createMock(MessageProviderInterface::class);
        $this->propertyIdFactory = new PropertyIdFactory($this->messageProvider);

        $this->messageProvider
            ->method('getMessage')
            ->willReturnCallback(function (string $key) {
                return match ($key) {
                    'property_id_must_be_positive' => 'Property ID должен быть положительным числом',
                    default => $key,
                };
            });
    }

    /**
     * @test
     */
    public function get_command_config_returns_correct_config(): void
    {
        $configuration = new ArrayCommandConfiguration([
            'accepted' => [
                'amount_property' => 14,
                'type_property' => 15,
                'some_param' => 'value',
            ],
            'close_reason' => [
                'close_reason_property' => 222,
            ],
        ], $this->propertyIdFactory);

        $config = $configuration->getCommandConfig('accepted');

        $this->assertNotNull($config);
        $this->assertEquals('accepted', $config->getCommandName());
        $this->assertEquals(new PropertyId(14), $config->getPropertyId('amount_property'));
        $this->assertEquals(new PropertyId(15), $config->getPropertyId('type_property'));
        $this->assertEquals('value', $config->getAdditionalParameter('some_param'));
    }

    /**
     * @test
     */
    public function get_command_config_returns_null_for_non_existent_command(): void
    {
        $configuration = new ArrayCommandConfiguration([], $this->propertyIdFactory);

        $config = $configuration->getCommandConfig('несуществующая');

        $this->assertNull($config);
    }

    /**
     * @test
     */
    public function get_command_config_handles_empty_configuration(): void
    {
        $configuration = new ArrayCommandConfiguration([
            'empty' => [],
        ], $this->propertyIdFactory);

        $config = $configuration->getCommandConfig('empty');

        $this->assertNotNull($config);
        $this->assertEquals('empty', $config->getCommandName());
        $this->assertEmpty($config->getPropertyIds());
        $this->assertEmpty($config->getAdditionalParameters());
    }

    /**
     * @test
     */
    public function property_id_parsing(): void
    {
        $configuration = new ArrayCommandConfiguration([
            'test' => [
                'amount_property' => 100,
                'type_property' => 200,
                'not_a_property' => 'string_value',
            ],
        ], $this->propertyIdFactory);

        $config = $configuration->getCommandConfig('test');

        $this->assertNotNull($config);
        $this->assertInstanceOf(CommandConfigDto::class, $config);
        $this->assertEquals(new PropertyId(100), $config->getPropertyId('amount_property'));
        $this->assertEquals(new PropertyId(200), $config->getPropertyId('type_property'));
        $this->assertNull($config->getPropertyId('not_a_property'));
        $this->assertEquals('string_value', $config->getAdditionalParameter('not_a_property'));
    }
}
