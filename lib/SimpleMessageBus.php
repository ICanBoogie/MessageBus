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
 * A message bus that can handle messages right away or push them to a queue.
 */
class SimpleMessageBus implements MessageBus
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
	 * @param Message $message
	 *
	 * @return mixed
	 */
	public function dispatch(Message $message)
	{
		if ($message instanceof MessageToPush)
		{
			return $this->push($message);
		}

		return $this->handle($message);
	}

	/**
	 * @param Message $message
	 *
	 * @return mixed
	 */
	protected function push(Message $message)
	{
		$pusher = $this->message_pusher;

		if (!$pusher)
		{
			throw new NoPusherForMessage($message);
		}

		return $pusher($message);
	}

	/**
	 * @param Message $message
	 *
	 * @return mixed
	 */
	protected function handle(Message $message)
	{
		$handler = $this->resolve_handler($message);

		return $handler($message);
	}

	/**
	 * @param Message $message
	 *
	 * @return MessageHandler|callable
	 *
	 * @throws NoHandlerForMessage
	 */
	protected function resolve_handler(Message $message)
	{
		$provider = $this->message_handler_provider;

		return $provider($message);
	}
}
