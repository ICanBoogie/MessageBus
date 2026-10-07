<?php

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
        $sut = new Context();
        $this->assertFalse($sut->has(Throwable::class));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageIs("Unable to find object matching: Throwable");

        $sut->get(Throwable::class);
    }

    public function testAddAndGet(): void
    {
        $sut = new Context([ $e2 = new BadMethodCallException() ]);
        $sut->add($e1 = new BadFunctionCallException());

        $this->assertTrue($sut->has(Throwable::class));
        $this->assertTrue($sut->has(BadFunctionCallException::class));
        $this->assertTrue($sut->has(BadMethodCallException::class));

        $this->assertSame($e1, $sut->get(Throwable::class));
        $this->assertSame($e1, $sut->get(BadFunctionCallException::class));
        $this->assertSame($e2, $sut->get(BadMethodCallException::class));
    }
}
