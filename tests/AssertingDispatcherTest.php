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
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @deprecated {@see https://github.com/ICanBoogie/MessageBus/issues/3}
 */
final class AssertingDispatcherTest extends TestCase
{
    use ProphecyTrait;

    private Dispatcher|ObjectProphecy $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = $this->prophesize(Dispatcher::class);

        parent::setUp();
    }

    public function testBubbleFailure(): void
    {
        $message = (object) [];
        $exception = new \Exception();

        $this->dispatcher->dispatch(Argument::any())
            ->shouldNotBeCalled();

        $dispatcher = $this->makeDispatcher(
            function ($actual) use ($message, $exception) {
                $this->assertSame($message, $actual);
                throw $exception;
            }
        );

        try {
            $dispatcher->dispatch($message);
        } catch (\Exception $e) {
            $this->assertSame($exception, $e);
            return;
        }

        $this->fail("Expected exception");
    }

    public function testDispatch(): void
    {
        $message = (object) [];
        $result = uniqid();

        $this->dispatcher->dispatch($message)
            ->shouldBeCalled()->willReturn($result);

        $dispatcher = $this->makeDispatcher(
            function ($actual) use ($message) {
                $this->assertSame($message, $actual);
            }
        );

        $this->assertSame($result, $dispatcher->dispatch($message));
    }

    private function makeDispatcher(callable $assertion): AssertingDispatcher
    {
        return new AssertingDispatcher($this->dispatcher->reveal(), $assertion);
    }
}
