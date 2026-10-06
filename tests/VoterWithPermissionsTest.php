<?php

namespace ICanBoogie\MessageBus;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
final class VoterWithPermissionsTest extends TestCase
{
    /**
     * @var MockObject&VoterProvider
     */
    private MockObject $voters;
    private Context $context;
    private object $message1;
    private object $message2;
    private object $message3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->voters = $this->createMock(VoterProvider::class);
        $this->context = new Context();
        $this->message1 = new class () {
        };
        $this->message2 = new class () {
        };
        $this->message3 = new class () {
        };
    }

    public function testNoPermission(): void
    {
        $actual = $this->makeSUT()->isGranted($this->message1, $this->context);

        $this->assertTrue($actual);
    }

    public function testNoVoter(): void
    {
        $actual = $this->makeSUT()->isGranted($this->message2, $this->context);

        $this->assertFalse($actual);
    }

    public function testVoterFalse(): void
    {
        $voter = $this->createMock(Voter::class);
        $voter
            ->expects($this->once())
            ->method('isGranted')
            ->with($this->message2, $this->context)
            ->willReturn(false);

        $this->voters
            ->expects($this->once())
            ->method('getVoterForPermission')
            ->with('perm1')
            ->willReturn($voter);

        $this->assertFalse($this->makeSUT()->isGranted($this->message2, $this->context));
    }

    public function testVoterTrue(): void
    {
        $voter = $this->createMock(Voter::class);
        $voter
            ->expects($this->once())
            ->method('isGranted')
            ->with($this->message3, $this->context)
            ->willReturn(true);

        $this->voters
            ->expects($this->once())
            ->method('getVoterForPermission')
            ->with('perm1')
            ->willReturn($voter);

        $this->assertTrue($this->makeSUT()->isGranted($this->message3, $this->context));
    }

    private function makeSUT(): Voter
    {
        return new VoterWithPermissions($this->voters, [
            $this->message2::class => [ 'perm1' ],
            $this->message3::class => [ 'perm1', 'perm2' ],
        ]);
    }
}
