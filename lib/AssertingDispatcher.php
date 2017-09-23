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
 * A Dispatcher decorator that asserts messages before dispatching them.
 */
class AssertingDispatcher implements Dispatcher
{
	/**
	 * @var Dispatcher
	 */
	private $dispatcher;

	/**
	 * @var callable
	 */
	private $assertion;

	/**
	 * @param Dispatcher $dispatcher
	 * @param callable $assertion A callable that should throw an exception if the message shouldn't
	 * be dispatched.
	 */
	public function __construct(Dispatcher $dispatcher, callable $assertion)
	{
		$this->dispatcher = $dispatcher;
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

		return $this->dispatcher->dispatch($message);
	}
}
