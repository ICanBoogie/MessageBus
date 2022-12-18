<?php

namespace Acme\MenuService\Application\MessageBus;

final class Permissions
{
    public const IS_ADMIN = 'is_admin';
    public const CAN_WRITE_MENU = 'can_write_menu';
    public const CAN_MANAGE_MENU = 'can_manage_menu';
}
