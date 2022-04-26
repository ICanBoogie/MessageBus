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
 * A permission voter.
 */
interface Voter
{
    /**
     * Returns `true` when permission is granted, ending the voting process positively.
     */
    public function isGranted(object $subject, Context $context): bool;
}
