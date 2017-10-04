<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus\Symfony;

use ICanBoogie\MessageBus\HandlerA;
use ICanBoogie\MessageBus\HandlerB;
use ICanBoogie\MessageBus\MessageA;
use ICanBoogie\MessageBus\MessageB;
use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MessageBusPassTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The `message` property is required for service `handler.message_a`
	 */
	public function test_should_error_on_missing_message()
	{
		$this->makeContainer(__DIR__ . '/resources/missing-message.yml');
	}

	/**
	 * @expectedException \LogicException
	 * @expectedExceptionMessage The command `ICanBoogie\MessageBus\MessageA` already has an handler: `handler.message_a`.
	 */
	public function test_should_error_message_duplicated()
	{
		$this->makeContainer(__DIR__ . '/resources/message-duplicate.yml');
	}

	public function test_should_return_handler()
	{
		/* @var ContainerHandlerProvider $provider */
		$container = $this->makeContainer(__DIR__ . '/resources/ok.yml');
		$provider = $container->get(MessageBusPass::DEFAULT_SERVICE_ID);

		$this->assertInstanceOf(ContainerHandlerProvider::class, $provider);
		$this->assertInstanceOf(HandlerA::class, $provider(new MessageA()));
		$this->assertInstanceOf(HandlerB::class, $provider(new MessageB()));
	}

	/**
	 * @param string $config
	 *
	 * @return SymfonyContainerBuilder
	 */
	private function makeContainer($config)
	{
		$container = new SymfonyContainerBuilder();
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
		$loader->load($config);
		$container->addCompilerPass(new MessageBusPass);
		$container->compile();

		return $container;
	}
}
