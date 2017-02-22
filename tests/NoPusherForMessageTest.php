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

class NoPusherForMessageTest extends \PHPUnit_Framework_TestCase
{
	public function testException()
	{
		$message = $this
			->getMockBuilder(ShouldBePushed::class)
			->getMockForAbstractClass();

		$exception = new NoPusherForMessage($message);

		$this->assertSame(400, $exception->getCode());
		$this->assertSame($message, $exception->message);
		$this->assertStringStartsWith("There is no message pusher defined", $exception->getMessage());
		$this->assertContains(get_class($message), $exception->getMessage());
	}
}
