<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus;

/**
 * A simple implementation of {@link HandlerProvider}.
 */
final class SimpleHandlerProvider implements HandlerProvider
{
	/**
	 * @var array<string, callable>
	 */
	private $handlers;

	/**
	 * @param array<string, callable> $handlers
	 */
	public function __construct(array $handlers)
	{
		$this->handlers = $handlers;
	}

	public function getHandlerForMessage(object $message): callable
	{
		$class = get_class($message);
		$handler = $this->handlers[$class] ?? null;

		if (!$handler)
		{
			throw new NoHandlerForMessage($message);
		}

		return $handler;
	}
}
