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

use ICanBoogie\MessageBus\PSR\QueryHandlerProvider;

/**
 * Register a query handler provider.
 *
 * ```yaml
 * services:
 *   Acme\MenuService\Application\Query\ShowMenuHandler:
 *     tags:
 *     - name: query_dispatcher.handler
 *       command: Acme\MenuService\Application\Query\ShowMenu
 * ```
 *
 * @deprecated {@see https://github.com/ICanBoogie/MessageBus/issues/3}
 */
final class QueryHandlerProviderPass extends HandlerProviderPass
{
    public const DEFAULT_SERVICE_ID = QueryHandlerProvider::class;
    public const DEFAULT_HANDLER_TAG = 'query_dispatcher.handler';
    public const DEFAULT_MESSAGE_PROPERTY = 'query';
    public const DEFAULT_PROVIDER_CLASS = QueryHandlerProvider::class;

    public function __construct(
        string $serviceId = self::DEFAULT_SERVICE_ID,
        string $handlerTag = self::DEFAULT_HANDLER_TAG,
        string $messageProperty = self::DEFAULT_MESSAGE_PROPERTY,
        string $providerClass = self::DEFAULT_PROVIDER_CLASS
    ) {
        parent::__construct($serviceId, $handlerTag, $messageProperty, $providerClass);
    }
}
