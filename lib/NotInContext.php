<?php

namespace ICanBoogie\MessageBus;

use LogicException;
use Throwable;

/**
 * Thrown when a {@see Context} doesn't have a matching object.
 */
class NotInContext extends LogicException implements Exception
{
    /**
     * @param class-string $class
     */
    public function __construct(
        public string $class,
        ?Throwable $previous = null
    ) {
        parent::__construct("Unable to find object matching: $class", 0, $previous);
    }
}
