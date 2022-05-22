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
use ICanBoogie\MessageBus\NotFound;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class HandlerProviderWithContainerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<ContainerInterface>
     */
    private ObjectProphecy $container;

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);

        parent::setUp();
    }

    public function testFailOnUndefinedHandler(): void
    {
        $this->container->get(Argument::any())
            ->shouldNotBeCalled();

        $provider = $this->makeProvider([]);

        $this->expectException(NotFound::class);

        $provider->getHandlerForMessage(new class () {
        });
    }

    public function testFailOnUndefinedService(): void
    {
        $messageA = new MessageA();
        $handlers = [

            get_class($messageA) => $undefinedService = uniqid(),

        ];

        $this->container->get($undefinedService)
            ->willThrow(new class extends Exception implements NotFoundExceptionInterface {
            });

        $provider = $this->makeProvider($handlers);

        $this->expectException(NotFound::class);

        $provider->getHandlerForMessage($messageA);
    }

    public function testReturnExpectedService(): void
    {
        $messageA = new MessageA();
        $messageB = new MessageB();
        $expectedService = function () {
        };
        $handlers = [

            get_class($messageA) => $expectedServiceId = uniqid(),
            get_class($messageB) => uniqid(),

        ];

        $this->container->get($expectedServiceId)
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
        return new HandlerProviderWithContainer($this->container->reveal(), $messageToHandler);
    }
}
