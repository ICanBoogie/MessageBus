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
 * A simple implementation of {@link HandlerProvider}.
 */
final class SimpleHandlerProvider implements HandlerProvider
{
    /**
     * @param array<string, callable> $handlers
     */
    public function __construct(
        private array $handlers
    ) {
    }

    public function getHandlerForMessage(object $message): callable
    {
        $class = get_class($message);

        return $this->handlers[$class]
            ?? throw new NotFound("No handler for messages of type `$class`.");
    }
}
