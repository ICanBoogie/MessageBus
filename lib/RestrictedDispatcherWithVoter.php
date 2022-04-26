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
 * A dispatcher with a voter, preferably one such as {@link VoterWithPermissions}.
 */
final class RestrictedDispatcherWithVoter implements RestrictedDispatcher
{
    public function __construct(
        private Dispatcher $innerDispatcher,
        private Voter $voter,
    ) {
    }

    /**
     * @throws PermissionNotGranted
     */
    public function dispatch(object $message, Context $context): mixed
    {
        $this->voter->isGranted($message, $context) or throw new PermissionNotGranted($message, $context);

        return $this->innerDispatcher->dispatch($message);
    }
}
