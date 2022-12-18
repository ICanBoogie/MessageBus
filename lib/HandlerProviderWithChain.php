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
 * A handler provider backed with a chain of providers.
 */
final class HandlerProviderWithChain implements HandlerProvider
{
    /**
     * @param iterable<HandlerProvider> $providers
     */
    public function __construct(
        private iterable $providers
    ) {
    }

    public function getHandlerForMessage(object $message): ?callable
    {
        foreach ($this->providers as $provider) {
            $handler = $provider->getHandlerForMessage($message);

            if ($handler) {
                return $handler;
            }
        }

        return null;
    }
}
