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

class SimpleMessageHandlerProviderTest extends \PHPUnit_Framework_TestCase
{
	public function test_should_throw_exception_on_missing_handler()
	{
		$messageA = new MessageA;
		$messageB = new MessageB;

		$message_handler_provider = new SimpleMessageHandlerProvider([

			get_class($messageA) => function ($message) {

				$this->fail("Should call another handler");

			}

		]);

		$bus = new SimpleDispatcher($message_handler_provider);

		try
		{
			$bus->dispatch($messageB);
		}
		catch (NoHandlerForMessage $e)
		{
			$this->assertSame($messageB, $e->message);
			return;
		}

		$this->fail("Expected NoHandlerForMessage");
	}

	public function test_should_return_the_expected_result()
	{
		$messageA = new MessageA;
		$messageB = new MessageB;

		$result = uniqid();

		$message_handler_provider = new SimpleMessageHandlerProvider([

			get_class($messageA) => function ($message) {

				$this->fail("Should call another handler");

			},

			get_class($messageB) => function ($message) use ($result, $messageB) {

				$this->assertSame($messageB, $message);

				return $result;

			},

		]);

		$bus = new SimpleDispatcher($message_handler_provider);

		$this->assertSame($result, $bus->dispatch($messageB));
	}
}
