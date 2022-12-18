<?php

namespace Acme\MenuService\Application\MessageBus;

use ICanBoogie\MessageBus\Attribute\Permission;

#[Permission(Permissions::IS_ADMIN)]
#[Permission(Permissions::CAN_WRITE_MENU)]
final class CreateMenu
{
    public function __construct(
        public /*readonly*/ int $id
    ) {
    }
}
