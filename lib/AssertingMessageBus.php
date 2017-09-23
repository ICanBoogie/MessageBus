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
 * A MessageBus decorator that asserts messages before dispatching them.
 */
class AssertingMessageBus implements MessageBus
{
	/**
	 * @var MessageBus
	 */
	private $message_bus;

	/**
	 * @var callable
	 */
	private $assertion;

	/**
	 * @param MessageBus $message_bus
	 * @param callable $assertion A callable that should throw an exception if the message shouldn't
	 * be dispatched.
	 */
	public function __construct(MessageBus $message_bus, callable $assertion)
	{
		$this->message_bus = $message_bus;
		$this->assertion = $assertion;
	}

	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	public function dispatch($message)
	{
		call_user_func($this->assertion, $message);

		return $this->message_bus->dispatch($message);
	}
}
