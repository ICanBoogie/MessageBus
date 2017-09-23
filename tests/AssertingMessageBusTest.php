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

use Prophecy\Argument;

class AssertingMessageBusTest extends \PHPUnit_Framework_TestCase
{
	use MockHelpers;

	public function test_should_not_dispatch_message()
	{
		$message = (object) [];
		$exception = new \Exception();

		$message_bus = $this->make_message_bus(
			function ($message_bus) {
				$message_bus->dispatch(Argument::any())
					->shouldNotBeCalled();
			},
			function ($actual) use ($message, $exception) {
				$this->assertSame($message, $actual);
				throw $exception;
			}
		);

		try
		{
			$message_bus->dispatch($message);
		} catch (\Exception $e) {
			$this->assertSame($exception, $e);
			return;
		}

		$this->fail("Expected exception");
	}

	public function test_should_dispatch_message()
	{
		$message = (object) [];
		$result = uniqid();

		$message_bus = $this->make_message_bus(
			function ($message_bus) use ($message, $result) {
				$message_bus->dispatch($message)
					->shouldBeCalled()->willReturn($result);
			},
			function ($actual) use ($message) {
				$this->assertSame($message, $actual);
			}
		);

		$this->assertSame($result, $message_bus->dispatch($message));
	}

	/**
	 * @param callable $init_message_bus
	 * @param callable $assertion
	 *
	 * @return AssertingMessageBus
	 */
	private function make_message_bus(callable $init_message_bus, callable $assertion)
	{
		return new AssertingMessageBus(
			$this->mock(MessageBus::class, $init_message_bus),
			$assertion
		);
	}
}
