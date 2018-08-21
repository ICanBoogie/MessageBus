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
 * Exception thrown when there is no handler defined to handle a message type.
 */
class NoHandlerForMessage extends \LogicException implements Exception
{
	public function __construct(object $message, \Throwable $previous = null)
	{
		parent::__construct($this->format_message($message), 400, $previous);
	}

	private function format_message(object $message): string
	{
		$class = get_class($message);

		return "There is no handler for messages of type `$class`";
	}
}
