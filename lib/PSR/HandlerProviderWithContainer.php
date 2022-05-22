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

use ICanBoogie\MessageBus\HandlerProvider;
use Psr\Container\ContainerInterface;

class HandlerProviderWithContainer implements HandlerProvider
{
    /**
     * @param array<class-string, string> $messageToHandler
     *     Where _key_ is a message class and _value_ the service identifier of its handler.
     */
    public function __construct(
        private ContainerInterface $container,
        private array $messageToHandler
    ) {
        $this->container = $container;
        $this->messageToHandler = $messageToHandler;
    }

    public function getHandlerForMessage(object $message): ?callable
    {
        $id = $this->messageToHandler[$message::class] ?? null;

        if (!$id) {
            return null;
        }

        return $this->container->get($id); // @phpstan-ignore-line
    }
}
