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

final class HandlerProviderWithHandlersTest extends TestCase
{
    private MessageB $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = new MessageB();
    }

    public function testNoHandler(): void
    {
        $handlerProvider = new HandlerProviderWithHandlers([
            MessageA::class => function () {
                $this->fail("This is not the handler you are looking for");
            }
        ]);

        $this->assertNull($handlerProvider->getHandlerForMessage($this->message));
    }

    public function testYesHandler(): void
    {
        $handlerProvider = new HandlerProviderWithHandlers([
            MessageA::class => function () {
                $this->fail("This is not the handler you are looking for");
            },

            MessageB::class => $handler = function () {
            }
        ]);

        $this->assertSame($handler, $handlerProvider->getHandlerForMessage($this->message));
    }
}
