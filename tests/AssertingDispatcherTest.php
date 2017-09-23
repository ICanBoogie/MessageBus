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

class AssertingDispatcherTest extends \PHPUnit_Framework_TestCase
{
	use MockHelpers;

	public function test_should_not_dispatch_message()
	{
		$message = (object) [];
		$exception = new \Exception();

		$dispatcher = $this->make_dispatcher(
			function ($dispatcher) {
				$dispatcher->dispatch(Argument::any())
					->shouldNotBeCalled();
			},
			function ($actual) use ($message, $exception) {
				$this->assertSame($message, $actual);
				throw $exception;
			}
		);

		try
		{
			$dispatcher->dispatch($message);
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

		$dispatcher = $this->make_dispatcher(
			function ($dispatcher) use ($message, $result) {
				$dispatcher->dispatch($message)
					->shouldBeCalled()->willReturn($result);
			},
			function ($actual) use ($message) {
				$this->assertSame($message, $actual);
			}
		);

		$this->assertSame($result, $dispatcher->dispatch($message));
	}

	/**
	 * @param callable $init_dispatcher
	 * @param callable $assertion
	 *
	 * @return AssertingDispatcher
	 */
	private function make_dispatcher(callable $init_dispatcher, callable $assertion)
	{
		return new AssertingDispatcher(
			$this->mock(Dispatcher::class, $init_dispatcher),
			$assertion
		);
	}
}
