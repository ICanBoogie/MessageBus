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
    public const DEFAULT_PARAMETER_FOR_PERMISSION_TO_VOTER = 'message_bus.permissions_by_message';
    public const DEFAULT_TAG_FOR_PERMISSION = 'message_bus.permission';
    public const DEFAULT_ATTRIBUTE_FOR_PERMISSION = 'permission';
    public const DEFAULT_PARAMETER_FOR_VOTERS = 'message_bus.permission_to_voter';
    public const DEFAULT_TAG_FOR_VOTER = 'message_bus.voter';

    /**
     * @param string $parameterForMessageToHandler
     * @param string $tagForHandler
     * @param string $attributeForMessage
     * @param string $parameterForPermissionToVoter
     * @param string $tagForPermission
     * @param string $attributeForPermission
     * @param string $parameterForVoter
     * @param string $tagForVoter
     */
    public function __construct(
        private string $parameterForMessageToHandler = self::DEFAULT_PARAMETER_FOR_MESSAGE_TO_HANDLER,
        private string $tagForHandler = self::DEFAULT_TAG_FOR_HANDLER,
        private string $attributeForMessage = self::DEFAULT_ATTRIBUTE_FOR_MESSAGE,
        private string $parameterForPermissionToVoter = self::DEFAULT_PARAMETER_FOR_PERMISSION_TO_VOTER,
        private string $tagForPermission = self::DEFAULT_TAG_FOR_PERMISSION,
        private string $attributeForPermission = self::DEFAULT_ATTRIBUTE_FOR_PERMISSION,
        private string $parameterForVoter = self::DEFAULT_PARAMETER_FOR_VOTERS,
        private string $tagForVoter = self::DEFAULT_TAG_FOR_VOTER,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        $this->processHandlersAndPermissions($container);
        $this->processVoters($container);
    }

    public function processHandlersAndPermissions(ContainerBuilder $container): void
    {
        $messageToHandler = [];
        $permissionsByMessage = [];

        foreach ($container->findTaggedServiceIds($this->tagForHandler) as $id => $hTags) {
            $message = $hTags[0][$this->attributeForMessage];
            $messageToHandler[$message] = $id;

            foreach ($container->findDefinition($id)->getTag($this->tagForPermission) as $pTags) {
                $permissionsByMessage[$message][] = $pTags[$this->attributeForPermission];
            }
        }

        $container->setParameter($this->parameterForMessageToHandler, $messageToHandler);
        $container->setParameter($this->parameterForPermissionToVoter, $permissionsByMessage);
    }

    public function processVoters(ContainerBuilder $container): void
    {
        $permissionToVoter = [];

        foreach ($container->findTaggedServiceIds($this->tagForVoter) as $id => $tags) {
            $permissionToVoter[$tags[0][$this->attributeForPermission]] = $id;
        }

        $container->setParameter($this->parameterForVoter, $permissionToVoter);
    }
}
