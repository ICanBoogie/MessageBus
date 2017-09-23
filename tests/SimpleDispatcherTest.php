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

		$pusher = function ($message) {

			$this->fail("The message should not be pushed");

		};

		$bus = new SimpleDispatcher($handler_provider, $pusher);
		$this->assertSame($result, $bus->dispatch($expectedMessage));
	}

	public function test_should_push_message()
	{
		$expectedMessage = $this
			->getMockBuilder(ShouldBePushed::class)
			->getMockForAbstractClass();

		$result = uniqid();

		$handler_provider = function ($message) {

			$this->fail("The message should not be handled");

		};

		$pusher = function ($message) use ($result) {

			return $result;

		};

		$bus = new SimpleDispatcher($handler_provider, $pusher);
		$this->assertSame($result, $bus->dispatch($expectedMessage));
	}

	public function test_should_throw_exception_when_pushing_message_without_pusher()
	{
		$message = $this
			->getMockBuilder(ShouldBePushed::class)
			->getMockForAbstractClass();

		$handler_provider = function ($message) {

			$this->fail("The message should not be handled");

		};

		$bus = new SimpleDispatcher($handler_provider);

		$this->expectException(NoPusherForMessage::class);
		$bus->dispatch($message);
	}
}
