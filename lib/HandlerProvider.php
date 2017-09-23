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
 * Signature example for the message handler provider callable.
 */
interface HandlerProvider
{
	/**
	 * @param object $message
	 *
	 * @return Handler|callable
	 *
	 * @throws NoHandlerForMessage if the handler for the message cannot the found.
	 */
	public function __invoke($message);
}
