<?php

namespace ICanBoogie\MessageBus;

interface RestrictedDispatcher
{
    /**
     * @throws PermissionNotGranted
     */
    public function dispatch(object $message, Context $context): mixed;
}
