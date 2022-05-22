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
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function uniqid;

final class DispatcherWithHandlerProviderTest extends TestCase
{
    use ProphecyTrait;

    private object $message;

    /**
     * @var ObjectProphecy<HandlerProvider>
     */
    private ObjectProphecy $handlerProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = (object) [ uniqid() => uniqid() ];
        $this->handlerProvider = $this->prophesize(HandlerProvider::class);
    }

    public function testFailOnMissingHandler(): void
    {
        $this->handlerProvider->getHandlerForMessage($this->message)
            ->shouldBeCalled()->willReturn(null);

        $stu = $this->makeSTU();

        $this->expectException(HandlerNotFound::class);
        $stu->dispatch($this->message);
    }

    public function testDispatch(): void
    {
        $result = uniqid();

        $this->handlerProvider->getHandlerForMessage($this->message)
            ->willReturn(fn() => $result);

        $stu = $this->makeSTU();
        $this->assertSame($result, $stu->dispatch($this->message));
    }

    private function makeSTU(): Dispatcher
    {
        return new DispatcherWithHandlerProvider(
            $this->handlerProvider->reveal()
        );
    }
}
