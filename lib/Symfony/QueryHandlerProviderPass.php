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
 */
final class QueryHandlerProviderPass extends HandlerProviderPass
{
	public const DEFAULT_SERVICE_ID = QueryHandlerProvider::class;
	public const DEFAULT_HANDLER_TAG = 'query_dispatcher.handler';
	public const DEFAULT_MESSAGE_PROPERTY = 'query';
	public const DEFAULT_PROVIDER_CLASS = QueryHandlerProvider::class;

	public function __construct(
		string $service_id = self::DEFAULT_SERVICE_ID,
		string $handler_tag = self::DEFAULT_HANDLER_TAG,
		string $message_property = self::DEFAULT_MESSAGE_PROPERTY,
		string $provider_class = self::DEFAULT_PROVIDER_CLASS
	) {
		parent::__construct($service_id, $handler_tag, $message_property, $provider_class);
	}
}
