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
 * An interface for message dispatchers.
 */
interface Dispatcher
{
	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	public function dispatch(object $message);
}
