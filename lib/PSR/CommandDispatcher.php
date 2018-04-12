<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus\PSR;

use ICanBoogie\MessageBus\SimpleDispatcher;

class CommandDispatcher extends SimpleDispatcher
{
	public function __construct(CommandHandlerProvider $handler_provider)
	{
		parent::__construct($handler_provider);
	}
}
