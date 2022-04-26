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

use function uniqid;

final class RestrictedDispatcherWithVoterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<Dispatcher>
     */
    private ObjectProphecy $innerDispatcher;

    /**
     * @var ObjectProphecy<Voter>
     */
    private ObjectProphecy $voter;
    private Context $context;
    private object $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->innerDispatcher = $this->prophesize(Dispatcher::class);
        $this->voter = $this->prophesize(Voter::class);
        $this->context = new Context();
        $this->message = new class () {
        };
    }

    public function testVoterFalse(): void
    {
        $this->voter->isGranted($this->message, $this->context)
            ->willReturn(false);
        $this->innerDispatcher->dispatch(Argument::any())
            ->shouldNotBeCalled();

        $this->expectException(PermissionNotGranted::class);

        $this->makeSTU()->dispatch($this->message, $this->context);
    }

    public function testVoterTrue(): void
    {
        $result = uniqid();

        $this->voter->isGranted($this->message, $this->context)
            ->willReturn(false);
        $this->innerDispatcher->dispatch($this->message)
            ->willReturn($result);

        $this->expectException(PermissionNotGranted::class);

        $this->assertSame(
            $result,
            $this->makeSTU()->dispatch($this->message, $this->context)
        );
    }

    private function makeSTU(): RestrictedDispatcher
    {
        return new RestrictedDispatcherWithVoter(
            $this->innerDispatcher->reveal(),
            $this->voter->reveal(),
        );
    }
}
