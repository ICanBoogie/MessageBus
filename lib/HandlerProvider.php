<?php

namespace ICanBoogie\MessageBus;

/**
 * A mapper from a message to its handler.
 */
interface HandlerProvider
{
    /**
     * @param object $message
     *   A message for which to return the relevant handler.
     *
     * @return callable|null
     *   A callable that MUST be type-compatible with $message.
     */
    public function getHandlerForMessage(object $message): ?callable;
}
