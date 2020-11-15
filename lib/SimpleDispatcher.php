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
 * A simple message dispatcher.
 */
class SimpleDispatcher implements Dispatcher
{
	/**
	 * @var HandlerProvider
	 */
	private $handler_provider;

	public function __construct(HandlerProvider $handler_provider)
	{
		$this->handler_provider = $handler_provider;
	}

	public function dispatch(object $message)
	{
		return $this->handler_provider->getHandlerForMessage($message)($message);
	}
}
