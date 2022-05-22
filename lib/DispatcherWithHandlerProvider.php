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
 * A message dispatcher backed with a {@link HandlerProvider}.
 */
final class DispatcherWithHandlerProvider implements Dispatcher
{
    public function __construct(
        private HandlerProvider $handlerProvider
    ) {
    }

    public function dispatch(object $message)
    {
        $class = $message::class;
        $handler = $this->handlerProvider->getHandlerForMessage($message)
            ?? throw new HandlerNotFound("No handler for messages of type `$class`");

        return $handler($message);
    }
}
