<?php

namespace ICanBoogie\MessageBus;

/**
 * A message dispatcher.
 */
interface Dispatcher
{
    /**
     * @param object $message
     *   The message to dispatch.
     *
     * @return mixed
     *   Result type depends on the handler.
     *
     * @throws HandlerNotFound
     *   The handler for the message cannot the found.
     */
    public function dispatch(object $message);
}
