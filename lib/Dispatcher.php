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
 * A message dispatcher.
 */
interface Dispatcher
{
	/**
	 * @param object $message
	 *   The message to dispatch.
	 *
	 * @return mixed
	 *   Result type depends on the handler.
	 *
	 * @throws NotFound
	 *   The handler for the message cannot the found.
	 */
	public function dispatch(object $message);
}
