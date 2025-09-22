<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Tests\Unit\Domain\ValueObjects;

use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealIdFactory;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DealIdTest extends TestCase
{
    protected MessageProviderInterface&MockObject $messageProvider;
    protected DealIdFactory $dealIdFactory;

    protected function setUp(): void
    {
        $this->messageProvider = $this->createMock(MessageProviderInterface::class);
        $this->dealIdFactory = new DealIdFactory($this->messageProvider);

        $this->messageProvider
            ->method('getMessage')
            ->willReturnCallback(function (string $key) {
                return match ($key) {
                    'deal_id_must_be_positive' => 'Deal ID должен быть положительным числом',
                    default => $key,
                };
            });
    }

    /**
     * @test
     */
    public function create_valid_deal_id(): void
    {
        $dealId = $this->dealIdFactory->create(123);

        $this->assertEquals(123, $dealId->getValue());
        $this->assertEquals('123', (string) $dealId);
    }

    /**
     * @test
     */
    public function create_deal_id_with_zero_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deal ID должен быть положительным числом');

        $this->dealIdFactory->create(0);
    }

    /**
     * @test
     */
    public function create_deal_id_with_negative_value_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Deal ID должен быть положительным числом');

        $this->dealIdFactory->create(-1);
    }

    public function equals(): void
    {
        $dealId1 = $this->dealIdFactory->create(123);
        $dealId2 = $this->dealIdFactory->create(123);
        $dealId3 = $this->dealIdFactory->create(456);

        $this->assertTrue($dealId1->equals($dealId2));
        $this->assertFalse($dealId1->equals($dealId3));
    }
}
