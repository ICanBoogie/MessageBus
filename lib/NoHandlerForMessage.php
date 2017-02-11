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

use ICanBoogie\Accessor\AccessorTrait;

/**
 * Exception thrown when there is no handler defined to handle a message type.
 *
 * @property-read object $message
 */
class NoHandlerForMessage extends \LogicException implements Exception
{
	use AccessorTrait;

	/**
	 * @var object
	 */
	private $_message;

	/**
	 * @return object
	 */
	protected function get_message()
	{
		return $this->_message;
	}

	/**
	 * @param object $message
	 * @param Exception|null $previous
	 */
	public function __construct($message, Exception $previous = null)
	{
		$this->_message = $message;

		parent::__construct($this->format_message($message), 400, $previous);
	}

	/**
	 * @param object $message
	 *
	 * @return string
	 */
	private function format_message($message)
	{
		$class = get_class($message);

		return "There is no handler defined to handle messages of type `$class`";
	}
}
