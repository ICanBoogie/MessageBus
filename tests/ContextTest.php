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

use BadFunctionCallException;
use BadMethodCallException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ContextTest extends TestCase
{
    public function testGetUndefined(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Unable to find object matching: Throwable");

        (new Context())->get(Throwable::class);
    }

    public function testAddAndGet(): void
    {
        $context = new Context([ $e2 = new BadMethodCallException() ]);
        $context->add($e1 = new BadFunctionCallException());

        $this->assertSame($e1, $context->get(Throwable::class));
        $this->assertSame($e1, $context->get(BadFunctionCallException::class));
        $this->assertSame($e2, $context->get(BadMethodCallException::class));
    }
}
