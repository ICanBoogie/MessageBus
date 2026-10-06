<?php

namespace ICanBoogie\MessageBus;

/**
 * A message dispatcher backed with a {@see HandlerProvider}.
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
