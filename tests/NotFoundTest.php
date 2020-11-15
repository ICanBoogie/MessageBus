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

use function uniqid;

class NotFoundTest extends TestCase
{
    public function testException()
    {
        $exception = new NotFound($message = uniqid(), $previous = new \Exception());

        $this->assertSame(0, $exception->getCode());
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
