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
	 * @var HandlerProvider
	 */
	private $handler_provider;

	/**
	 * @param HandlerProvider $handler_provider
	 */
	public function __construct(HandlerProvider $handler_provider)
	{
		$this->handler_provider = $handler_provider;
	}

	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	public function dispatch($message)
	{
		$handler = $this->resolve_handler($message);

		return $handler($message);
	}

	/**
	 * @param object $message
	 *
	 * @return Handler|callable
	 *
	 * @throws NoHandlerForMessage
	 */
	protected function resolve_handler($message)
	{
		$provider = $this->handler_provider;

		return $provider($message);
	}
}
