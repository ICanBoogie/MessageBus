<?php

namespace Acme\MenuService\Presentation\Security\Voters;

use Acme\MenuService\Application\MessageBus\Permissions;
use ICanBoogie\MessageBus\Attribute\Vote;
use ICanBoogie\MessageBus\Context;
use ICanBoogie\MessageBus\Voter;

#[Vote(Permissions::CAN_WRITE_MENU)]
final class CanWriteMenu implements Voter
{
    public function isGranted(object $message, Context $context): bool
    {
        return true;
    }
}
