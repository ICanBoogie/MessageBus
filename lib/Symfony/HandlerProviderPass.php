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

use ICanBoogie\MessageBus\HandlerProvider;
use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\TypedReference;

use function is_string;

/**
 * Collect message handlers and register their provider.
 */
class HandlerProviderPass implements CompilerPassInterface
{
    public const DEFAULT_SERVICE_ID = HandlerProvider::class;
    public const DEFAULT_HANDLER_TAG = 'message_dispatcher.handler';
    public const DEFAULT_MESSAGE_PROPERTY = 'message';
    public const DEFAULT_PROVIDER_CLASS = ContainerHandlerProvider::class;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $handlerTag;

    /**
     * @var string
     */
    private $messageProperty;

    /**
     * @var string
     */
    private $providerClass;

    public function __construct(
        string $serviceId = self::DEFAULT_SERVICE_ID,
        string $handlerTag = self::DEFAULT_HANDLER_TAG,
        string $messageProperty = self::DEFAULT_MESSAGE_PROPERTY,
        string $providerClass = self::DEFAULT_PROVIDER_CLASS
    ) {
        $this->serviceId = $serviceId;
        $this->handlerTag = $handlerTag;
        $this->messageProperty = $messageProperty;
        $this->providerClass = $providerClass;
    }

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container): void
    {
        [ $mapping, $refMap ] = $this->collectHandlers($container);

        $container
            ->register($this->serviceId, $this->providerClass)
            ->setArguments([
                ServiceLocatorTagPass::register($container, $refMap),
                $mapping
            ]);
    }

    /**
     * @return array{0: array<string, string>, 1: array<string, TypedReference>}
     */
    private function collectHandlers(ContainerBuilder $container): array
    {
        $handlers = $container->findTaggedServiceIds($this->handlerTag, true);
        $messageProperty = $this->messageProperty;
        $mapping = [];
        $refMap = [];

        foreach ($handlers as $id => $tags) {
            assert(is_string($id));

            $command = $tags[0][$messageProperty] ?? null;

            if (!$command) {
                throw new InvalidArgumentException(
                    "The `$messageProperty` property is required for service `$id`."
                );
            }

            assert(is_string($command));

            if (isset($mapping[$command])) {
                throw new LogicException(
                    "The command `$command` already has an handler: `{$mapping[$command]}`."
                );
            }

            $class = $container->getDefinition($id)->getClass();

            if (!$class) {
                throw new LogicException("Unable to get class of service `$id`.");
            }

            $mapping[$command] = $id;
            $refMap[$id] = new TypedReference($id, $class);
        }

        return [ $mapping, $refMap ];
    }
}
