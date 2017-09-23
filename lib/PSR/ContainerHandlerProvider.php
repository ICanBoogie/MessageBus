<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus\PSR;

use ICanBoogie\MessageBus\NoHandlerForMessage;
use ICanBoogie\MessageBus\SimpleHandlerProvider;
use Psr\Container\ContainerInterface;

/**
 * A simple implementation of {@link HandlerProvider}.
 */
class ContainerHandlerProvider extends SimpleHandlerProvider
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param array $handlers
	 * @param ContainerInterface $container
	 */
	public function __construct(array $handlers, ContainerInterface $container)
	{
		parent::__construct($handlers);

		$this->container = $container;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke($message)
	{
		$id = parent::__invoke($message);

		if (!$this->container->has($id)) {
			throw new NoHandlerForMessage($message);
		}

		return $this->container->get($id);
	}
}
