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

use Throwable;

class PermissionNotGranted extends \Exception implements Exception
{
    /**
     * @param Context $context
     */
    public function __construct(
        public object $dispatched_message,
        public Context $context,
        ?Throwable $previous = null
    ) {
        parent::__construct("Permission not granted for message: " . $dispatched_message::class, 0, $previous);
    }
}
