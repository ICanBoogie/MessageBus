<?php

namespace ICanBoogie\MessageBus\Attribute;

use Attribute;

/**
 * Identifies a voter and the permission it votes for.
 *
 * @readonly
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Vote
{
    public function __construct(
        public string $permission,
    ) {
    }
}
