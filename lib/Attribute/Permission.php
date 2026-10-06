<?php

namespace ICanBoogie\MessageBus\Attribute;

use Attribute;

/**
 * Identifies a permission required to dispatch a message.
 *
 * A message can have multiple {@see Permission}s.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class Permission
{
    public function __construct(
        public string $permission
    ) {
    }
}
