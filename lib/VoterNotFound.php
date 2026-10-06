<?php

namespace ICanBoogie\MessageBus;

use LogicException;
use Throwable;

/**
 * Thrown when a voter cannot be found for a permission.
 */
class VoterNotFound extends LogicException implements Exception
{
    public function __construct(
        public string $permission,
        ?Throwable $previous = null
    ) {
        parent::__construct("Voter not found for permission: $permission", previous: $previous);
    }
}
