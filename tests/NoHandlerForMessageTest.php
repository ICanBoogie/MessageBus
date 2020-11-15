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

class NoHandlerForMessageTest extends TestCase
{
	public function testException()
	{
		$message = (object) [ uniqid() => uniqid() ];
		$exception = new NoHandlerForMessage($message);

		$this->assertSame(400, $exception->getCode());
		$this->assertStringStartsWith("There is no handler for", $exception->getMessage());
		$this->assertStringContainsString(get_class($message), $exception->getMessage());
	}
}
