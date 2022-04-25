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
 * A simple message dispatcher.
 */
class SimpleDispatcher implements Dispatcher
{
    public function __construct(
        private HandlerProvider $handlerProvider
    ) {
    }

    public function dispatch(object $message)
    {
        return $this->handlerProvider->getHandlerForMessage($message)($message);
    }
}
