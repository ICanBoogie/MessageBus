<?php

namespace ICanBoogie\MessageBus;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function uniqid;

final class DispatcherWithHandlerProviderTest extends TestCase
{
    private object $message;

    /**
     * @var MockObject&HandlerProvider
     */
    private MockObject $handlerProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = (object) [ uniqid() => uniqid() ];
        $this->handlerProvider = $this->createMock(HandlerProvider::class);
    }

    public function testFailOnMissingHandler(): void
    {
        $this->handlerProvider
            ->expects($this->once())
            ->method('getHandlerForMessage')
            ->with($this->message)
            ->willReturn(null);

        $stu = $this->makeSUT();

        $this->expectException(HandlerNotFound::class);
        $stu->dispatch($this->message);
    }

    public function testDispatch(): void
    {
        $result = uniqid();

        $this->handlerProvider
            ->expects($this->once())
            ->method('getHandlerForMessage')
            ->with($this->message)
            ->willReturn(fn() => $result);

        $stu = $this->makeSUT();
        $this->assertSame($result, $stu->dispatch($this->message));
    }

    private function makeSUT(): Dispatcher
    {
        return new DispatcherWithHandlerProvider(
            $this->handlerProvider
        );
    }
}
