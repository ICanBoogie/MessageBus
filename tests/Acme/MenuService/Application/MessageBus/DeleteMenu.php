<?php

namespace Acme\MenuService\Application\MessageBus;

use ICanBoogie\MessageBus\Attribute\Permission;

#[Permission(Permissions::IS_ADMIN)]
#[Permission(Permissions::CAN_MANAGE_MENU)]
final class DeleteMenu
{
    public function __construct(
        public /*readonly*/ int $id
    ) {
    }
}
