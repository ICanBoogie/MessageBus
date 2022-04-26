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

final class VoterWithPermissionsTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<VoterProvider>
     */
    private ObjectProphecy $voters;
    private Context $context;
    private object $message1;
    private object $message2;
    private object $message3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->voters = $this->prophesize(VoterProvider::class);
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
        $this->assertTrue($this->makeSTU()->isGranted($this->message1, $this->context));
    }

    public function testNoVoter(): void
    {
        $this->assertFalse($this->makeSTU()->isGranted($this->message2, $this->context));
    }

    public function testVoterFalse(): void
    {
        $voter = $this->prophesize(Voter::class);
        $voter->isGranted($this->message2, $this->context)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->voters
            ->getVoterForPermission('perm1')
            ->willReturn($voter);

        $this->assertFalse($this->makeSTU()->isGranted($this->message2, $this->context));
    }

    public function testVoterTrue(): void
    {
        $voter = $this->prophesize(Voter::class);
        $voter->isGranted($this->message3, $this->context)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->voters
            ->getVoterForPermission('perm1')
            ->willReturn($voter);

        $this->voters
            ->getVoterForPermission('perm2')
            ->shouldNotBeCalled();

        $this->assertTrue($this->makeSTU()->isGranted($this->message3, $this->context));
    }

    private function makeSTU(): Voter
    {
        return new VoterWithPermissions($this->voters->reveal(), [
            $this->message2::class => [ 'perm1' ],
            $this->message3::class => [ 'perm1', 'perm2' ],
        ]);
    }
}
