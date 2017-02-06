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

class NoHandlerForMessageTest extends \PHPUnit_Framework_TestCase
{
	public function testException()
	{
		$message = $this
			->getMockBuilder(Message::class)
			->getMockForAbstractClass();

		/* @var Message $message */

		$exception = new NoHandlerForMessage($message);

		$this->assertSame(400, $exception->getCode());
		$this->assertSame($message, $exception->message);
		$this->assertStringStartsWith("There is no handler defined", $exception->getMessage());
		$this->assertContains(get_class($message), $exception->getMessage());
	}
}
