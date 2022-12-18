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
 * Identifies a permission required to dispatch a message.
 *
 * A message can have multiple {@link Permission}s.
 *
 * @readonly
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Permission
{
    public function __construct(
        public string $permission
    ) {
    }
}
