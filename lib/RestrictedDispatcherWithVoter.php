<?php

namespace ICanBoogie\MessageBus;

/**
 * A dispatcher with a voter, preferably one such as {@see VoterWithPermissions}.
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
