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

/**
 * A voter with the knowledge of permissions attached to messages.
 */
final class VoterWithPermissions implements Voter
{
    /**
     * @param array<class-string, string[]> $permissionsByMessage
     *     Where _key_ is a command class and _value_ the permissions for that message.
     */
    public function __construct(
        private VoterProvider $voters,
        private array $permissionsByMessage,
    ) {
    }

    public function isGranted(object $message, Context $context): bool
    {
        $permissions = $this->permissionsByMessage[$message::class] ?? null;

        if (!$permissions) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->voters->getVoterForPermission($permission)?->isGranted($message, $context)) {
                return true;
            }
        }

        return false;
    }
}
