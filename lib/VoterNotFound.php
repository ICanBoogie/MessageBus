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
        parent::__construct("Voter not found for permission: $permission", 0, $previous);
    }
}
