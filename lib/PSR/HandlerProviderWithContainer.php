<?php

namespace ICanBoogie\MessageBus\PSR;

use ICanBoogie\MessageBus\HandlerProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class HandlerProviderWithContainer implements HandlerProvider
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHandlerForMessage(object $message): ?callable
    {
        $id = $this->messageToHandler[$message::class] ?? null;

        if (!$id) {
            return null;
        }

        return $this->container->get($id); // @phpstan-ignore-line
    }
}
