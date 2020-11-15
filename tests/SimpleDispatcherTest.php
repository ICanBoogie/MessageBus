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

class SimpleDispatcherTest extends TestCase
{
    public function testDispatch()
    {
        $expectedMessage = (object) [ uniqid() => uniqid() ];
        $result = uniqid();

        $handlerProvider = $this->prophesize(HandlerProvider::class);
        $handlerProvider->getHandlerForMessage($expectedMessage)
            ->shouldBeCalled()->willReturn(function () use ($result) {
                return $result;
            });

        $bus = new SimpleDispatcher($handlerProvider->reveal());
        $this->assertSame($result, $bus->dispatch($expectedMessage));
    }
}
