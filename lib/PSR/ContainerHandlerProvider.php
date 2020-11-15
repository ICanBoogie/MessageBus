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

use function get_class;

class ContainerHandlerProvider implements HandlerProvider
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array<string, string>
     */
    private $handlers;

    /**
     * @param array<string, string> $mapping
     *   An array of key/value pairs where _key_ is a message class and _value_ the service
     *   identifier of its handler.
     */
    public function __construct(ContainerInterface $container, array $mapping)
    {
        $this->handlers = $mapping;
        $this->container = $container;
    }

    public function getHandlerForMessage(object $message): callable
    {
        $class = get_class($message);
        $id = $this->handlers[$class] ?? null;

        if (!$id) {
            throw new NotFound("No handler for messages of type `$class`.");
        }

        try {
            return $this->container->get($id);
        } catch (NotFoundExceptionInterface $e) {
            throw new NotFound("No handler for messages of type `$class`.", $e);
        }
    }
}
