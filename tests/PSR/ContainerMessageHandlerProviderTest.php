<?php

namespace ICanBoogie\MessageBus\PSR;

use ICanBoogie\MessageBus\MessageA;
use ICanBoogie\MessageBus\MessageB;
use ICanBoogie\MessageBus\MessageHandlerProvider;
use ICanBoogie\MessageBus\MockHelpers;
use Psr\Container\ContainerInterface;

class ContainerMessageHandlerProviderTest extends \PHPUnit_Framework_TestCase
{
	use MockHelpers;

	/**
	 * @expectedException \ICanBoogie\MessageBus\NoHandlerForMessage
	 */
	public function test_should_throw_exception_on_undefined_service()
	{
		$messageA = new MessageA();
		$handlers = [

			get_class($messageA) => $undefined_service = uniqid(),

		];

		$provider = $this->makeProvider(
			$handlers,
			function ($container) use ($undefined_service) {
				$container->has($undefined_service)
					->shouldBeCalledTimes(1)->willReturn(false);
				$container->get($undefined_service)
					->shouldNotBeCalled();
			}
		);

		$provider($messageA);
	}

	public function test_should_return_expected_service()
	{
		$messageA = new MessageA();
		$messageB = new MessageB();
		$expected_service = (object) [];
		$handlers = [

			get_class($messageA) => $expected_service_id = uniqid(),
			get_class($messageB) => uniqid(),

		];

		$provider = $this->makeProvider(
			$handlers,
			function ($container) use ($expected_service_id, $expected_service) {
				$container->has($expected_service_id)
					->shouldBeCalledTimes(1)->willReturn(true);
				$container->get($expected_service_id)
					->shouldBeCalledTimes(1)->willReturn($expected_service);
			}
		);

		$this->assertSame($expected_service, $provider($messageA));
	}

	/**
	 * @param array $handlers
	 * @param callable|null $initContainer
	 *
	 * @return MessageHandlerProvider
	 */
	private function makeProvider(array $handlers, callable $initContainer = null)
	{
		return new ContainerMessageHandlerProvider(
			$handlers,
			$this->mockContainer($initContainer)
		);
	}

	/**
	 * @param callable|null $init
	 *
	 * @return ContainerInterface
	 */
	private function mockContainer(callable $init = null)
	{
		return $this->mock(ContainerInterface::class, $init);
	}
}
