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
 * A handler provider backend with pairs of message class/handlers.
 */
final class HandlerProviderWithHandlers implements HandlerProvider
{
    /**
     * @param array<class-string, callable> $handlers
     *     Where _key_ is a message class and _value_ a handler for that message class.
     */
    public function __construct(
        private array $handlers
    ) {
    }

    public function getHandlerForMessage(object $message): ?callable
    {
        return $this->handlers[$message::class] ?? null;
    }
}
