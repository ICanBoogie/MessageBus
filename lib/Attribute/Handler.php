<?php

namespace ICanBoogie\MessageBus\Attribute;

use Attribute;

/**
 * Identifies a message handler.
 *
 * **Note:** The message type supported by the handler is inferred from its `__invoke` method.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Handler
{
}
