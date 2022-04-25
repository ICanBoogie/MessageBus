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
 * Thrown when a {@link Context} doesn't have a matching object.
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
