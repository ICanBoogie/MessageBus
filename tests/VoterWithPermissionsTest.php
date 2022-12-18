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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
        $this->assertTrue($this->makeSUT()->isGranted($this->message1, $this->context));
    }

    public function testNoVoter(): void
    {
        $this->assertFalse($this->makeSUT()->isGranted($this->message2, $this->context));
    }

    public function testVoterFalse(): void
    {
        $voter = $this->createMock(Voter::class);
        $voter
            ->method('isGranted')
            ->with($this->message2, $this->context)
            ->willReturn(false);

        $this->voters
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
