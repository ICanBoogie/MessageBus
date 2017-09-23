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
class AddCommandBusPass implements CompilerPassInterface
{
	const DEFAULT_PROVIDER_SERVICE = 'message_bus.handler_provider';
	const DEFAULT_HANDLER_TAG = 'message_bus.handler';
	const DEFAULT_MESSAGE_PROPERTY = 'message';

	/**
	 * @var string
	 */
	private $providerId;

	/**
	 * @var string
	 */
	private $handlerTag;

	/**
	 * @var string
	 */
	private $messageProperty;

	/**
	 * @param string $providerId
	 * @param string $handlerTag
	 * @param string $messageProperty
	 */
	public function __construct(
		$providerId = self::DEFAULT_PROVIDER_SERVICE,
		$handlerTag = self::DEFAULT_HANDLER_TAG,
		$messageProperty = self::DEFAULT_MESSAGE_PROPERTY
	) {
		$this->providerId = $providerId;
		$this->handlerTag = $handlerTag;
		$this->messageProperty = $messageProperty;
	}

	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
		$handlers = $container->findTaggedServiceIds($this->handlerTag, true);
		$messageProperty = $this->messageProperty;
		$mapping = [];
		$handlerRefs = [];

		foreach ($handlers as $id => $tags) {
			if (empty($tags[0][$messageProperty]))
			{
				throw new InvalidArgumentException(
					"The `$messageProperty` property is required for service `$id`."
				);
			}

			$command = $tags[0][$messageProperty];

			if (isset($mapping[$command]))
			{
				throw new LogicException(
					"The command `$command` already has an handler: `{$mapping[$command]}`."
				);
			}

			$mapping[$command] = $id;
			$handlerRefs[$id] = new TypedReference($id, $container->getDefinition($id)->getClass());
		}

		$container
			->register($this->providerId, ContainerHandlerProvider::class)
			->setArguments([
				$mapping,
				ServiceLocatorTagPass::register($container, $handlerRefs)
			]);
	}
}
