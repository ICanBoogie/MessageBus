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
 * The interface for the message bus.
 */
interface MessageBus
{
	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	public function dispatch($message);
}
