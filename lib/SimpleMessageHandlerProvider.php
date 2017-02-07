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
 * A simple implementation of {@link MessageHandlerProvider}.
 */
class SimpleMessageHandlerProvider implements MessageHandlerProvider
{
	/**
	 * @var array
	 */
	private $handlers;

	/**
	 * @param MessageHandler[]|callable[] $handlers
	 */
	public function __construct(array $handlers)
	{
		$this->handlers = $handlers;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke(Message $message)
	{
		$class = get_class($message);

		if (empty($this->handlers[$class]))
		{
			throw new NoHandlerForMessage($message);
		}

		return $this->handlers[$class];
	}
}
