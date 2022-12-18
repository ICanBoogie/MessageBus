<?php

namespace Acme\MenuService\Application\MessageBus;

use ICanBoogie\MessageBus\Attribute\Handler;

#[Handler]
final class CreateMenuHandler
{
    public function __invoke(CreateMenu $message): void
    {
    }
}
