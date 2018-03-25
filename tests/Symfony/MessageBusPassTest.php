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
use ICanBoogie\MessageBus\HandlerProvider;
use ICanBoogie\MessageBus\MessageA;
use ICanBoogie\MessageBus\MessageB;
use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function uniqid;

class MessageBusPassTest extends \PHPUnit\Framework\TestCase
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
		$container = $this->makeContainer(__DIR__ . '/resources/ok.yml', $alias = 'alias_' . uniqid());
		$provider = $container->get($alias);

		$this->assertInstanceOf(ContainerHandlerProvider::class, $provider);
		$this->assertInstanceOf(HandlerA::class, $provider(new MessageA()));
		$this->assertInstanceOf(HandlerB::class, $provider(new MessageB()));
	}

	private function makeContainer(string $config, string $alias = null): SymfonyContainerBuilder
	{
		$container = new SymfonyContainerBuilder();
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
		$loader->load($config);
		$container->addCompilerPass(new MessageBusPass);

		if ($alias)
		{
			$container->setAlias($alias, new Alias(HandlerProvider::class, true));
		}

		$container->compile();

		return $container;
	}
}