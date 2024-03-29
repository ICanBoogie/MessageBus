<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus\PSR;

use ICanBoogie\MessageBus\Voter;
use ICanBoogie\MessageBus\VoterNotFound;
use ICanBoogie\MessageBus\VoterProvider;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

final class VoterProviderWithContainerTest extends TestCase
{
    private const PERMISSION_IS_ADMIN = 'is_admin';
    private const PERMISSION_CAN_CREATE_MENU = 'can_create_menu';
    private const VOTER_IS_ADMIN_CLASS = 'Acme\\Presentation\\Security\\Voters\\IsAdmin';
    private const VOTER_CAN_CREATE_MENU_CLASS = 'Acme\\Presentation\\Security\\Voters\\CanCreateMenu';

    private Voter $voterIsAdmin;

    /**
     * @var MockObject&ContainerInterface
     */
    private MockObject $container;
    private Throwable $containerException;

    protected function setUp(): void
    {
        parent::setUp();

        $this->voterIsAdmin = $this->createMock(Voter::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->containerException = new LogicException();
        $this->container
            ->method('get')
            ->willReturnCallback(fn (string $permission) => match ($permission) {
                self::VOTER_IS_ADMIN_CLASS => $this->voterIsAdmin,
                default => throw $this->containerException,
            });
    }

    public function testFailureOnMissingServiceId(): void
    {
        $this->expectException(VoterNotFound::class);
        $this->expectExceptionMessage("Voter not found for permission: is_madonna");

        $this->makeSUT()->getVoterForPermission('is_madonna');
    }

    /**
     * @throws Throwable
     */
    public function testFailureOnMissingService(): void
    {
        try {
            $this->makeSUT()->getVoterForPermission(self::PERMISSION_CAN_CREATE_MENU);
            $this->fail("Expected exception");
        } catch (Throwable $e) {
            $this->assertSame($this->containerException, $e->getPrevious());

            $this->expectException(VoterNotFound::class);
            $this->expectExceptionMessage("Voter not found for permission: can_create_menu");

            throw $e;
        }
    }

    public function testGetVoterForPermission(): void
    {
        $this->assertSame(
            $this->voterIsAdmin,
            $this->makeSUT()->getVoterForPermission(self::PERMISSION_IS_ADMIN)
        );
    }

    private function makeSUT(): VoterProvider
    {
        return new VoterProviderWithContainer($this->container, [
            self::PERMISSION_IS_ADMIN => self::VOTER_IS_ADMIN_CLASS,
            self::PERMISSION_CAN_CREATE_MENU => self::VOTER_CAN_CREATE_MENU_CLASS,
        ]);
    }
}
