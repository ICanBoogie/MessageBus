<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus\PSR;

use ICanBoogie\MessageBus\Voter;
use ICanBoogie\MessageBus\VoterNotFound;
use ICanBoogie\MessageBus\VoterProvider;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * Provides permission voters from a service container.
 */
final class VoterProviderWithContainer implements VoterProvider
{
    /**
     * @param array<string, string> $permissionToService
     *     Where _key_ is a permission and _value_ a service.
     */
    public function __construct(
        private ContainerInterface $container,
        private array $permissionToService,
    ) {
    }

    public function getVoterForPermission(string $permission): ?Voter
    {
        $id = $this->permissionToService[$permission]
            ?? throw new VoterNotFound($permission);

        try {
            return $this->container->get($id); // @phpstan-ignore-line
        } catch (Throwable $e) {
            throw new VoterNotFound($permission, $e);
        }
    }
}
