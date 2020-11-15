<?php

namespace ICanBoogie\MessageBus\PSR;

use ICanBoogie\MessageBus\HandlerProvider;
use ICanBoogie\MessageBus\MessageA;
use ICanBoogie\MessageBus\MessageB;
use ICanBoogie\MessageBus\NoHandlerForMessage;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

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

	public function test_should_throw_exception_on_undefined_service()
	{
		$messageA = new MessageA();
		$handlers = [

			get_class($messageA) => $undefined_service = uniqid(),

		];

		$this->container->has($undefined_service)
			->shouldBeCalledTimes(1)->willReturn(false);
		$this->container->get($undefined_service)
			->shouldNotBeCalled();

		$provider = $this->makeProvider($handlers);

		$this->expectException(NoHandlerForMessage::class);

		$provider->getHandlerForMessage($messageA);
	}

	public function test_should_return_expected_service()
	{
		$messageA = new MessageA();
		$messageB = new MessageB();
		$expected_service = function () {};
		$handlers = [

			get_class($messageA) => $expected_service_id = uniqid(),
			get_class($messageB) => uniqid(),

		];

		$this->container->has($expected_service_id)
			->shouldBeCalledTimes(1)->willReturn(true);
		$this->container->get($expected_service_id)
			->shouldBeCalledTimes(1)->willReturn($expected_service);

		$provider = $this->makeProvider($handlers);

		$this->assertSame($expected_service, $provider->getHandlerForMessage($messageA));
	}

	private function makeProvider(array $handlers): HandlerProvider
	{
		return new ContainerHandlerProvider(
			$this->container->reveal(), $handlers
		);
	}
}
