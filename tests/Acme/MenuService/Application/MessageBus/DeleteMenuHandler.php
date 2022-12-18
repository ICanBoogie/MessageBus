<?php

namespace Acme\MenuService\Application\MessageBus;

use ICanBoogie\MessageBus\Attribute\Handler;

#[Handler]
final class DeleteMenuHandler
{
    public function __invoke(DeleteMenu $message): void
    {
    }
}
