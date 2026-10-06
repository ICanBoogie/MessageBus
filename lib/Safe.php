<?php

namespace ICanBoogie\MessageBus;

/**
 * A message that doesn't alter the state of an application. In other words, a message that leads to a read-only
 * operation.
 */
interface Safe
{
}
