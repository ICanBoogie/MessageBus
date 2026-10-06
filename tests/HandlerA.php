<?php

namespace ICanBoogie\MessageBus;

final class HandlerA
{
    public function __invoke(MessageA $message): void
    {
    }
}
