<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\MessageBus\Symfony;

use ICanBoogie\MessageBus\PSR\CommandHandlerProvider;

/**
 * Register a command handler provider.
 *
 * ```yaml
 * services:
 *   Acme\MenuService\Application\Command\ActivateMenuHandler:
 *     tags:
 *     - name: command_dispatcher.handler
 *       command: Acme\MenuService\Application\Command\ActivateMenu
 * ```
 */
final class CommandHandlerProviderPass extends HandlerProviderPass
{
    public const DEFAULT_SERVICE_ID = CommandHandlerProvider::class;
    public const DEFAULT_HANDLER_TAG = 'command_dispatcher.handler';
    public const DEFAULT_MESSAGE_PROPERTY = 'command';
    public const DEFAULT_PROVIDER_CLASS = CommandHandlerProvider::class;

    public function __construct(
        string $serviceId = self::DEFAULT_SERVICE_ID,
        string $handlerTag = self::DEFAULT_HANDLER_TAG,
        string $messageProperty = self::DEFAULT_MESSAGE_PROPERTY,
        string $providerClass = self::DEFAULT_PROVIDER_CLASS
    ) {
        parent::__construct($serviceId, $handlerTag, $messageProperty, $providerClass);
    }
}
