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
 * A dispatcher that dispatches messages right away or push them to a queue.
 */
class SimpleDispatcher implements Dispatcher
{
	/**
	 * @var MessageHandlerProvider|callable
	 */
	private $message_handler_provider;

	/**
	 * @var MessagePusher|callable
	 */
	private $message_pusher;

	/**
	 * @param MessageHandlerProvider|callable $message_handler_provider
	 * @param MessagePusher|callable $message_pusher
	 */
	public function __construct(callable $message_handler_provider, callable $message_pusher = null)
	{
		$this->message_handler_provider = $message_handler_provider;
		$this->message_pusher = $message_pusher;
	}

	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	public function dispatch($message)
	{
		if ($message instanceof ShouldBePushed)
		{
			return $this->push($message);
		}

		return $this->handle($message);
	}

	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	protected function push($message)
	{
		$pusher = $this->message_pusher;

		if (!$pusher)
		{
			throw new NoPusherForMessage($message);
		}

		return $pusher($message);
	}

	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	protected function handle($message)
	{
		$handler = $this->resolve_handler($message);

		return $handler($message);
	}

	/**
	 * @param object $message
	 *
	 * @return MessageHandler|callable
	 *
	 * @throws NoHandlerForMessage
	 */
	protected function resolve_handler($message)
	{
		$provider = $this->message_handler_provider;

		return $provider($message);
	}
}
