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
 * Signature example for the message handler callable.
 */
interface Handler
{
	/**
	 * @param object $message
	 *
	 * @return mixed
	 */
	public function __invoke($message);
}
