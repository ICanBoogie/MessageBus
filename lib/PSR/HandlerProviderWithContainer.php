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
use ICanBoogie\MessageBus\NotFound;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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

    public function getHandlerForMessage(object $message): callable
    {
        $class = $message::class;
        $id = $this->messageToHandler[$class]
            ?? throw new NotFound("No handler for messages of type `$class`.");

        try {
            return $this->container->get($id); // @phpstan-ignore-line
        } catch (NotFoundExceptionInterface $e) {
            throw new NotFound("No handler for messages of type `$class`.", $e);
        }
    }
}
