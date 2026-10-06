<?php

namespace ICanBoogie\MessageBus;

use LogicException;
use Throwable;

/**
 * Thrown when a handler for a message cannot be found.
 */
class HandlerNotFound extends LogicException implements Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}
