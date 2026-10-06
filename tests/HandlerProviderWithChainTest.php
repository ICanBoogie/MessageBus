<?php

namespace ICanBoogie\MessageBus;

use PHPUnit\Framework\TestCase;

final class HandlerProviderWithChainTest extends TestCase
{
    public function testChain(): void
    {
        $messageA = new MessageA();
        $messageB = new MessageB();
        $messageC = new class () {
        };

        $handler1 = function () {
        };
        $handler2 = function () {
        };

        $provider1 = $this->createMock(HandlerProvider::class);
        $provider1
            ->expects($this->exactly(3))
            ->method('getHandlerForMessage')
            ->willReturnCallback(fn(object $message) => match ($message) {
                $messageA => $handler1,
                default => null
            });

        $provider2 = $this->createMock(HandlerProvider::class);
        $provider2
            ->expects($this->exactly(2))
            ->method('getHandlerForMessage')
            ->willReturnCallback(fn (object $message) => match ($message) {
                $messageB => $handler2,
                default => null
            });

        $provider = new HandlerProviderWithChain([ $provider1, $provider2 ]);

        $this->assertSame($handler1, $provider->getHandlerForMessage($messageA));
        $this->assertSame($handler2, $provider->getHandlerForMessage($messageB));
        $this->assertNull($provider->getHandlerForMessage($messageC));
    }
}
