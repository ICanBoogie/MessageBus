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
 * A mapper from a message to its handler.
 */
interface HandlerProvider
{
    /**
     * @param object $message
     *   A message for which to return the relevant handler.
     *
     * @return callable
     *   A callable that MUST be type-compatible with $message.
     *
     * @throws NotFound
     *   The handler for the message cannot the found.
     */
    public function getHandlerForMessage(object $message): callable;
}
