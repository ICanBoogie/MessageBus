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

use ICanBoogie\MessageBus\Attribute;
use LogicException;
use olvlvl\ComposerAttributeCollector\Attributes;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

use function assert;

/**
 * Creates handler and voter services according to their attributes.
 *
 * This compiler pass is meant to run before {@link MessageBusPass}.
 */
final class MessageBusPassWithAttributes implements CompilerPassInterface
{
    public function __construct(
        private string $tagForHandler = MessageBusPass::DEFAULT_TAG_FOR_HANDLER,
        private string $attributeForMessage = MessageBusPass::DEFAULT_ATTRIBUTE_FOR_MESSAGE,
        private string $tagForPermission = MessageBusPass::DEFAULT_TAG_FOR_PERMISSION,
        private string $attributeForPermission = MessageBusPass::DEFAULT_ATTRIBUTE_FOR_PERMISSION,
        private string $tagForVoter = MessageBusPass::DEFAULT_TAG_FOR_VOTER,
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        /** @var array<class-string, Definition> $definitions */
        $definitions = [];

        /**
         * @var array<class-string, string[]> $permissions
         *     Where _key_ is a command and _value_ its associated permissions.
         */
        $permissions = [];

        foreach (Attributes::findTargetClasses(Attribute\Permission::class) as $targetClass) {
            $permissions[$targetClass->name][] = $targetClass->attribute->permission;
        }

        foreach (Attributes::findTargetClasses(Attribute\Handler::class) as $targetClass) {
            $handler = $targetClass->name;
            $message = self::resolveMessage($handler);

            $definition = new Definition($handler);
            $definition->addTag($this->tagForHandler, [ $this->attributeForMessage => $message ]);

            foreach ($permissions[$message] ?? [] as $permission) {
                $definition->addTag($this->tagForPermission, [ $this->attributeForPermission => $permission ]);
            }

            $definitions[$handler] = $definition;
        }

        foreach (Attributes::findTargetClasses(Attribute\Vote::class) as $targetClass) {
            $voter = $targetClass->name;
            $permission = $targetClass->attribute->permission;

            $definition = new Definition($voter);
            $definition->addTag($this->tagForVoter, [ $this->attributeForPermission => $permission ]);

            $definitions[$voter] = $definition;
        }

        $container->addDefinitions($definitions);
    }

    /**
     * @param class-string $handler
     *
     * @return class-string
     *
     * @throws ReflectionException
     */
    private static function resolveMessage(string $handler): string
    {
        $type = (new ReflectionMethod($handler, '__invoke'))
            ->getParameters()[0]
            ->getType() ?? throw new LogicException("Expected a type for the first argument");

        assert($type instanceof ReflectionNamedType);

        // @phpstan-ignore-next-line
        return $type->getName();
    }
}
