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

	public function test_should_fail_on_undefined_handler()
	{
		$this->container->get(Argument::any())
			->shouldNotBeCalled();

		$provider = $this->makeProvider([]);

		$this->expectException(NotFound::class);

		$provider->getHandlerForMessage(new class () {});
	}

	public function test_should_fail_on_undefined_service()
	{
		$messageA = new MessageA();
		$handlers = [

			get_class($messageA) => $undefined_service = uniqid(),

		];

		$this->container->get($undefined_service)
			->willThrow(new class extends Exception implements NotFoundExceptionInterface {});

		$provider = $this->makeProvider($handlers);

		$this->expectException(NotFound::class);

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

		$this->container->get($expected_service_id)
			->willReturn($expected_service);

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
