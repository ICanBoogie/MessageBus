<?php

namespace ICanBoogie\MessageBus;

use PHPUnit\Framework\TestCase;

use function uniqid;

final class HandlerNotFoundTest extends TestCase
{
    public function testException(): void
    {
        $exception = new HandlerNotFound($message = uniqid(), $previous = new \Exception());

        $this->assertSame(0, $exception->getCode());
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
