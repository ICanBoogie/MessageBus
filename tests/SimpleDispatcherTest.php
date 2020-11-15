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
	public function test_should_handle_message()
	{
		$expectedMessage = (object) [ uniqid() => uniqid() ];
		$result = uniqid();

		$handler_provider = $this->prophesize(HandlerProvider::class);
		$handler_provider->getHandlerForMessage($expectedMessage)
			->shouldBeCalled()->willReturn(function () use ($result) {
				return $result;
			});

		$bus = new SimpleDispatcher($handler_provider->reveal());
		$this->assertSame($result, $bus->dispatch($expectedMessage));
	}
}
