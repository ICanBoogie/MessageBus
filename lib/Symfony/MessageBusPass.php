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

use ICanBoogie\MessageBus\HandlerProvider;
use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\TypedReference;

/**
 * Registers command bus handlers.
 */
class MessageBusPass implements CompilerPassInterface
{
	const DEFAULT_SERVICE_ID = HandlerProvider::class;
	const DEFAULT_HANDLER_TAG = 'message_bus.handler';
	const DEFAULT_MESSAGE_PROPERTY = 'message';

	/**
	 * @var string
	 */
	private $service_id;

	/**
	 * @var string
	 */
	private $handler_tag;

	/**
	 * @var string
	 */
	private $message_property;

	/**
	 * @param string $service_id
	 * @param string $handler_tag
	 * @param string $message_property
	 */
	public function __construct(
		$service_id = self::DEFAULT_SERVICE_ID,
		$handler_tag = self::DEFAULT_HANDLER_TAG,
		$message_property = self::DEFAULT_MESSAGE_PROPERTY
	) {
		$this->service_id = $service_id;
		$this->handler_tag = $handler_tag;
		$this->message_property = $message_property;
	}

	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
		$handlers = $container->findTaggedServiceIds($this->handler_tag, true);
		$message_property = $this->message_property;
		$mapping = [];
		$ref_map = [];

		foreach ($handlers as $id => $tags) {
			if (empty($tags[0][$message_property]))
			{
				throw new InvalidArgumentException(
					"The `$message_property` property is required for service `$id`."
				);
			}

			$command = $tags[0][$message_property];

			if (isset($mapping[$command]))
			{
				throw new LogicException(
					"The command `$command` already has an handler: `{$mapping[$command]}`."
				);
			}

			$mapping[$command] = $id;
			$ref_map[$id] = new TypedReference($id, $container->getDefinition($id)->getClass());
		}

		$container
			->register($this->service_id, ContainerHandlerProvider::class)
			->setArguments([
				$mapping,
				ServiceLocatorTagPass::register($container, $ref_map)
			]);
	}
}
