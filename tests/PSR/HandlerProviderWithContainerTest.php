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

use Exception;
use ICanBoogie\MessageBus\HandlerProvider;
use ICanBoogie\MessageBus\MessageA;
use ICanBoogie\MessageBus\MessageB;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class HandlerProviderWithContainerTest extends TestCase
{
    /**
     * @var MockObject&ContainerInterface
     */
    private MockObject $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);

        parent::setUp();
    }

    public function testFailOnUndefinedHandler(): void
    {
        $this->container
            ->expects($this->never())
            ->method('get');

        $provider = $this->makeProvider([]);

        $this->assertNull(
            $provider->getHandlerForMessage(new class () {
            })
        );
    }

    public function testFailOnUndefinedService(): void
    {
        $messageA = new MessageA();
        $handlers = [

            $messageA::class => $undefinedService = uniqid(),

        ];

        $this->container
            ->method('get')
            ->with($undefinedService)
            ->willThrowException(new class extends Exception implements NotFoundExceptionInterface {
            });

        $provider = $this->makeProvider($handlers);

        $this->expectException(NotFoundExceptionInterface::class);

        $provider->getHandlerForMessage($messageA);
    }

    public function testReturnExpectedService(): void
    {
        $messageA = new MessageA();
        $messageB = new MessageB();
        $expectedService = function () {
        };
        $handlers = [

            $messageA::class => $expectedServiceId = uniqid(),
            $messageB::class => uniqid(),

        ];

        $this->container
            ->method('get')
            ->with($expectedServiceId)
            ->willReturn($expectedService);

        $provider = $this->makeProvider($handlers);

        $this->assertSame($expectedService, $provider->getHandlerForMessage($messageA));
    }

    /**
     * @param array<class-string, string> $messageToHandler
     *     Where _key_ is a message class and _value_ the service identifier of its handler.
     */
    private function makeProvider(array $messageToHandler): HandlerProvider
    {
        return new HandlerProviderWithContainer($this->container, $messageToHandler);
    }
}
