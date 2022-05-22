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

use InvalidArgumentException;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * A compiler pass that inspect message bus handlers and voters to create mapping parameters.
 *
 * The following parameters are created (names are customizable):
 *
 * - `message_bus.message_to_handler`: array<class-string, string> where _key_ is the class of a message and _value_
 *   the service identifier of a message handler.
 * - `message_bus.permissions_by_message`: array<class-string, string[]> where _key_ is the class of a message and
 *   _value_ an array of permissions.
 * - `message_bus.permission_to_voter`: array<string, string> where _key_ is a permission and _value_ the service
 *   identifier of a voter.
 */
final class MessageBusPass implements CompilerPassInterface
{
    public const DEFAULT_PARAMETER_FOR_MESSAGE_TO_HANDLER = 'message_bus.message_to_handler';
    public const DEFAULT_TAG_FOR_HANDLER = 'message_bus.handler';
    public const DEFAULT_ATTRIBUTE_FOR_MESSAGE = 'message';
    public const DEFAULT_PARAMETER_FOR_PERMISSIONS_BY_MESSAGE = 'message_bus.permissions_by_message';
    public const DEFAULT_TAG_FOR_PERMISSION = 'message_bus.permission';
    public const DEFAULT_ATTRIBUTE_FOR_PERMISSION = 'permission';
    public const DEFAULT_PARAMETER_FOR_PERMISSION_TO_VOTER = 'message_bus.permission_to_voter';
    public const DEFAULT_TAG_FOR_VOTER = 'message_bus.voter';

    public function __construct(
        private string $parameterForMessageToHandler = self::DEFAULT_PARAMETER_FOR_MESSAGE_TO_HANDLER,
        private string $tagForHandler = self::DEFAULT_TAG_FOR_HANDLER,
        private string $attributeForMessage = self::DEFAULT_ATTRIBUTE_FOR_MESSAGE,
        private string $parameterForPermissionsByMessage = self::DEFAULT_PARAMETER_FOR_PERMISSIONS_BY_MESSAGE,
        private string $tagForPermission = self::DEFAULT_TAG_FOR_PERMISSION,
        private string $attributeForPermission = self::DEFAULT_ATTRIBUTE_FOR_PERMISSION,
        private string $parameterForPermissionToVoter = self::DEFAULT_PARAMETER_FOR_PERMISSION_TO_VOTER,
        private string $tagForVoter = self::DEFAULT_TAG_FOR_VOTER,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        $this->processHandlersAndPermissions($container);
        $this->processVoters($container);
    }

    /**
     * Builds a map of message class to handler service identifier,
     * and another of permissions by message class.
     */
    private function processHandlersAndPermissions(ContainerBuilder $container): void
    {
        $messageToHandler = [];
        $permissionsByMessage = [];

        foreach ($container->findTaggedServiceIds($this->tagForHandler) as $id => $hTags) {
            $message = $hTags[0][$this->attributeForMessage]
                ?? throw new InvalidArgumentException(
                    "Missing attribute '$this->attributeForMessage' for service '$id'"
                );

            $duplicate = $messageToHandler[$message] ?? null;

            if ($duplicate) {
                throw new LogicException("Unable to register handler '$id'"
                    . ", the handler '$duplicate' is already registered for message class '$message'");
            }

            $messageToHandler[$message] = $id;

            foreach ($container->findDefinition($id)->getTag($this->tagForPermission) as $pTags) {
                $permissionsByMessage[$message][] = $pTags[$this->attributeForPermission];
            }
        }

        $container->setParameter($this->parameterForMessageToHandler, $messageToHandler);
        $container->setParameter($this->parameterForPermissionsByMessage, $permissionsByMessage);
    }

    /**
     * Builds a map of permission to voter service identifier.
     */
    private function processVoters(ContainerBuilder $container): void
    {
        $permissionToVoter = [];

        foreach ($container->findTaggedServiceIds($this->tagForVoter) as $id => $tags) {
            $permissionToVoter[$tags[0][$this->attributeForPermission]] = $id;
        }

        $container->setParameter($this->parameterForPermissionToVoter, $permissionToVoter);
    }
}
