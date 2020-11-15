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
 * Collect message handlers and register their provider.
 */
class HandlerProviderPass implements CompilerPassInterface
{
	public const DEFAULT_SERVICE_ID = HandlerProvider::class;
	public const DEFAULT_HANDLER_TAG = 'message_dispatcher.handler';
	public const DEFAULT_QUERY_PROPERTY = 'message';

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

	public function __construct(
		string $service_id = self::DEFAULT_SERVICE_ID,
		string $handler_tag = self::DEFAULT_HANDLER_TAG,
		string $message_property = self::DEFAULT_QUERY_PROPERTY
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
		[ $mapping, $ref_map ] = $this->collectHandlers($container);

		$container
			->register($this->service_id, ContainerHandlerProvider::class)
			->setArguments([
				$mapping,
				ServiceLocatorTagPass::register($container, $ref_map)
			]);
	}

	protected function collectHandlers(ContainerBuilder $container): array
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

		return [ $mapping, $ref_map ];
	}
}
