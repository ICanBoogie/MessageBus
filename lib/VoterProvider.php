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
