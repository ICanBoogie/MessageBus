<?php

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
