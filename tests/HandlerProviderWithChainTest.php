<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class HandlerProviderWithChainTest extends TestCase
{
    use ProphecyTrait;

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

        $provider1 = $this->prophesize(HandlerProvider::class);
        $provider1->getHandlerForMessage($messageA)
            ->willReturn($handler1);
        $provider1->getHandlerForMessage($messageB)
            ->willReturn(null);
        $provider1->getHandlerForMessage($messageC)
            ->willReturn(null);

        $provider2 = $this->prophesize(HandlerProvider::class);
        $provider2->getHandlerForMessage($messageB)
            ->willReturn($handler2);
        $provider2->getHandlerForMessage($messageC)
            ->willReturn(null);

        $provider = new HandlerProviderWithChain([ $provider1->reveal(), $provider2->reveal() ]);

        $this->assertSame($handler1, $provider->getHandlerForMessage($messageA));
        $this->assertSame($handler2, $provider->getHandlerForMessage($messageB));
        $this->assertNull($provider->getHandlerForMessage($messageC));
    }
}
