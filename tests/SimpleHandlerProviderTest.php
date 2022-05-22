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

use PHPUnit\Framework\TestCase;

final class SimpleHandlerProviderTest extends TestCase
{
    public function testFailOnMissingHandler(): void
    {
        $messageA = new MessageA();
        $messageB = new MessageB();

        $handlerProvider = new SimpleHandlerProvider([
            get_class($messageA) => function () {
                $this->fail("This is not the handler you are looking for");
            }
        ]);

        $bus = new DispatcherWithHandlerProvider($handlerProvider);

        try {
            $bus->dispatch($messageB);
        } catch (NotFound $e) {
            $this->assertStringContainsString(get_class($messageB), $e->getMessage());
            return;
        }

        $this->fail("Expected NotFound");
    }

    public function testDispatch(): void
    {
        $messageA = new MessageA();
        $messageB = new MessageB();

        $result = uniqid();

        $handlerProvider = new SimpleHandlerProvider([
            get_class($messageA) => function () {
                $this->fail("This is not the handler you are looking for");
            },

            get_class($messageB) => function ($message) use ($result, $messageB) {
                $this->assertSame($messageB, $message);

                return $result;
            },
        ]);

        $bus = new DispatcherWithHandlerProvider($handlerProvider);

        $this->assertSame($result, $bus->dispatch($messageB));
    }
}
