<?php

namespace ICanBoogie\MessageBus;

/**
 * The context in which a message is dispatched.
 */
final class Context
{
    /**
     * @var object[]
     */
    private array $objects = [];

    /**
     * @param iterable<object> $objects
     */
    public function __construct(iterable $objects = [])
    {
        foreach ($objects as $object) {
            $this->add($object);
        }
    }

    /**
     * Adds an object to the context.
     */
    public function add(object $object): self
    {
        array_unshift($this->objects, $object);

        return $this;
    }

    /**
     * Whether an object matching the specified class exists in the context.
     *
     * @template T of object
     *
     * @param class-string<T> $class
     */
    public function has(string $class): bool
    {
        foreach ($this->objects as $object) {
            if ($object instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the object matching the specified class.
     *
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     *
     * @throws NotInContext
     */
    public function get(string $class): object
    {
        foreach ($this->objects as $object) {
            if ($object instanceof $class) {
                return $object;
            }
        }

        throw new NotInContext($class);
    }
}
