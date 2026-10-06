<?php

namespace ICanBoogie\MessageBus;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function uniqid;

final class RestrictedDispatcherWithVoterTest extends TestCase
{
    /**
     * @var MockObject&Dispatcher
     */
    private MockObject $innerDispatcher;

    /**
     * @var MockObject&Voter
     */
    private MockObject $voter;
    private Context $context;
    private object $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->innerDispatcher = $this->createMock(Dispatcher::class);
        $this->voter = $this->createMock(Voter::class);
        $this->context = new Context();
        $this->message = new class () {
        };
    }

    public function testVoterFalse(): void
    {
        $this->voter
            ->expects($this->once())
            ->method('isGranted')
            ->with($this->message, $this->context)
            ->willReturn(false);
        $this->innerDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(PermissionNotGranted::class);

        $this->makeSUT()->dispatch($this->message, $this->context);
    }

    public function testVoterTrue(): void
    {
        $result = uniqid();

        $this->voter
            ->expects($this->once())
            ->method('isGranted')
            ->with($this->message, $this->context)
            ->willReturn(true);
        $this->innerDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->message)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->makeSUT()->dispatch($this->message, $this->context)
        );
    }

    private function makeSUT(): RestrictedDispatcher
    {
        return new RestrictedDispatcherWithVoter(
            $this->innerDispatcher,
            $this->voter,
        );
    }
}
