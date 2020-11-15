<?php

namespace ICanBoogie\MessageBus\PSR;

use Exception;
use ICanBoogie\MessageBus\HandlerProvider;
use ICanBoogie\MessageBus\MessageA;
use ICanBoogie\MessageBus\MessageB;
use ICanBoogie\MessageBus\NotFound;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContainerHandlerProviderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ContainerInterface
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);

        parent::setUp();
    }

    public function testFailOnUndefinedHandler()
    {
        $this->container->get(Argument::any())
            ->shouldNotBeCalled();

        $provider = $this->makeProvider([]);

        $this->expectException(NotFound::class);

        $provider->getHandlerForMessage(new class () {
        });
    }

    public function testFailOnUndefinedService()
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

    public function testReturnExpectedService()
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

    private function makeProvider(array $handlers): HandlerProvider
    {
        return new ContainerHandlerProvider($this->container->reveal(), $handlers);
    }
}
