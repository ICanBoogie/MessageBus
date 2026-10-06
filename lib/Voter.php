<?php

namespace ICanBoogie\MessageBus;

/**
 * A permission voter.
 */
interface Voter
{
    /**
     * Returns `true` when permission is granted, ending the voting process positively.
     */
    public function isGranted(object $message, Context $context): bool;
}
