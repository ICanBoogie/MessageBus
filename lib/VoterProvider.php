<?php

namespace ICanBoogie\MessageBus;

interface VoterProvider
{
    /**
     * Returns a voter for a permission.
     *
     * The dispatcher should fail if the function returns `null`, but a chain of voter providers
     * could invoke the next voter provider instead.
     */
    public function getVoterForPermission(string $permission): ?Voter;
}
