<?php

namespace ICanBoogie\MessageBus;

class SimpleMessageBusTest extends \PHPUnit_Framework_TestCase
{
	public function testHandleMessage()
	{
		$expectedMessage = $this
			->getMockBuilder(Message::class)
			->getMockForAbstractClass();

		$result = uniqid();

		$handler = function (Message $message) use ($result) {

			return $result;

		};

		$handler_provider = function (Message $message) use (
			$expectedMessage,
			$handler
		) {

			if ($message === $expectedMessage)
			{
				return $handler;
			}

			throw new NoHandlerForMessage($message);

		};

		$pusher = function (Message $message) {

			$this->fail("The message should not be pushed");

		};

		$bus = new SimpleMessageBus($handler_provider, $pusher);

		/* @var Message $expectedMessage */

		$this->assertSame($result, $bus->dispatch($expectedMessage));
	}

	public function testPushMessage()
	{
		$expectedMessage = $this
			->getMockBuilder(MessageToPush::class)
			->getMockForAbstractClass();

		$result = uniqid();

		$handler_provider = function (Message $message) {

			$this->fail("The message should not be handled");

		};

		$pusher = function (Message $message) use ($result) {

			return $result;

		};

		$bus = new SimpleMessageBus($handler_provider, $pusher);

		/* @var Message $expectedMessage */

		$this->assertSame($result, $bus->dispatch($expectedMessage));
	}

	public function testShouldThrowExceptionWhenPushingMessageWithoutPusher()
	{
		$message = $this
			->getMockBuilder(MessageToPush::class)
			->getMockForAbstractClass();

		$handler_provider = function (Message $message) {

			$this->fail("The message should not be handled");

		};

		$bus = new SimpleMessageBus($handler_provider);

		/* @var Message $message */

		$this->expectException(NoPusherForMessage::class);
		$bus->dispatch($message);
	}
}
