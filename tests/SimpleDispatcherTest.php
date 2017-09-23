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

class SimpleDispatcherTest extends \PHPUnit_Framework_TestCase
{
	public function test_should_handle_message()
	{
		$expectedMessage = (object) [ uniqid() => uniqid() ];
		$result = uniqid();

		$handler = function ($message) use ($result) {

			return $result;

		};

		$handler_provider = function ($message) use (
			$expectedMessage,
			$handler
		) {

			if ($message === $expectedMessage)
			{
				return $handler;
			}

			throw new NoHandlerForMessage($message);

		};

		$bus = new SimpleDispatcher($handler_provider);
		$this->assertSame($result, $bus->dispatch($expectedMessage));
	}
}
