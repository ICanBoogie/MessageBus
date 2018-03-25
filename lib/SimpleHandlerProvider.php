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
class SimpleHandlerProvider implements HandlerProvider
{
	/**
	 * @var array
	 */
	private $handlers;

	public function __construct(array $handlers)
	{
		$this->handlers = $handlers;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke(object $message)
	{
		$class = get_class($message);

		if (empty($this->handlers[$class]))
		{
			throw new NoHandlerForMessage($message);
		}

		return $this->handlers[$class];
	}
}