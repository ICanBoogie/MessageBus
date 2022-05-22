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

final class DispatcherWithHandlerProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testDispatch(): void
    {
        $expectedMessage = (object) [ uniqid() => uniqid() ];
        $result = uniqid();

        $handlerProvider = $this->prophesize(HandlerProvider::class);
        $handlerProvider->getHandlerForMessage($expectedMessage)
            ->shouldBeCalled()->willReturn(fn() => $result);

        $bus = new DispatcherWithHandlerProvider($handlerProvider->reveal());
        $this->assertSame($result, $bus->dispatch($expectedMessage));
    }
}
