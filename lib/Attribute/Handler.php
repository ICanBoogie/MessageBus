<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus\Attribute;

use Attribute;

/**
 * Identifies a message handler.
 *
 * **Note:** The message type supported by the handler is inferred from its `__invoke` method.
 *
 * @readonly
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Handler
{
}
