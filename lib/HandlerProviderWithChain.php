<?php

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
