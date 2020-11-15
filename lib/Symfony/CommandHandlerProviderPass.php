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

use ICanBoogie\MessageBus\PSR\CommandHandlerProvider;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CommandHandlerProviderPass extends HandlerProviderPass
{
	public const DEFAULT_SERVICE_ID = CommandHandlerProvider::class;
	public const DEFAULT_HANDLER_TAG = 'command_dispatcher.handler';
	public const DEFAULT_QUERY_PROPERTY = 'command';

	/**
	 * @var string
	 */
	private $service_id;

	public function __construct(
		string $service_id = self::DEFAULT_SERVICE_ID,
		string $handler_tag = self::DEFAULT_HANDLER_TAG,
		string $command_property = self::DEFAULT_QUERY_PROPERTY
	) {
		$this->service_id = $service_id;

		parent::__construct($service_id, $handler_tag, $command_property);
	}

	/**
	 * @inheritdoc
	 */
	public function process(ContainerBuilder $container)
	{
		[ $mapping, $ref_map ] = $this->collectHandlers($container);

		$container
			->register($this->service_id, CommandHandlerProvider::class)
			->setArguments([
				$mapping,
				ServiceLocatorTagPass::register($container, $ref_map)
			]);
	}
}
