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
 * A Dispatcher decorator that asserts messages before dispatching them.
 *
 * @deprecated {@see https://github.com/ICanBoogie/MessageBus/issues/3}
 */
class AssertingDispatcher implements Dispatcher
{
    /**
     * @var callable
     */
    private $assertion;

    /**
     * @param callable $assertion
     *     A callable that should throw an exception if the message shouldn't be dispatched.
     */
    public function __construct(
        private Dispatcher $dispatcher,
        callable $assertion
    ) {
        $this->dispatcher = $dispatcher;
        $this->assertion = $assertion;
    }

    /**
     * @param object $message
     *
     * @return mixed
     */
    public function dispatch(object $message)
    {
        ($this->assertion)($message);

        return $this->dispatcher->dispatch($message);
    }
}
