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

/**
 * @deprecated {@see https://github.com/ICanBoogie/MessageBus/issues/3}
 */
class QueryDispatcher extends SimpleDispatcher
{
    public function __construct(QueryHandlerProvider $handlerProvider)
    {
        parent::__construct($handlerProvider);
    }
}
