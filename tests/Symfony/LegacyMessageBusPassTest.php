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

use ICanBoogie\MessageBus\HandlerA;
use ICanBoogie\MessageBus\HandlerB;
use ICanBoogie\MessageBus\HandlerProvider;
use ICanBoogie\MessageBus\MessageA;
use ICanBoogie\MessageBus\MessageB;
use ICanBoogie\MessageBus\PSR\CommandDispatcher;
use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;
use ICanBoogie\MessageBus\PSR\QueryDispatcher;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function uniqid;

/**
 * @deprecated {@see https://github.com/ICanBoogie/MessageBus/issues/3}
 */
final class LegacyMessageBusPassTest extends TestCase
{
    public function testFailOnMissingMessage(): void
    {
        $this->expectExceptionMessage("The `message` property is required for service `handler.message_a`");
        $this->expectException(InvalidArgumentException::class);
        $this->makeContainer(__DIR__ . '/resources/missing-message.yml');
    }

    public function testFailOnDuplicateMessage(): void
    {
        $this->expectExceptionMessage(
            "The command `ICanBoogie\MessageBus\MessageA` already has an handler: `handler.message_a`."
        );
        $this->expectException(LogicException::class);
        $this->makeContainer(__DIR__ . '/resources/message-duplicate.yml');
    }

    public function testProvider(): void
    {
        /* @var ContainerHandlerProvider $provider */
        $container = $this->makeContainer(__DIR__ . '/resources/ok.yml', $alias = 'alias_' . uniqid());
        $provider = $container->get($alias);

        $this->assertInstanceOf(ContainerHandlerProvider::class, $provider);
        $this->assertInstanceOf(HandlerA::class, $provider->getHandlerForMessage(new MessageA()));
        $this->assertInstanceOf(HandlerB::class, $provider->getHandlerForMessage(new MessageB()));
    }

    public function testCQS(): void
    {
        $container = new SymfonyContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load(__DIR__ . '/resources/cqs.yml');
        $container
            ->addCompilerPass(new QueryHandlerProviderPass())
            ->addCompilerPass(new CommandHandlerProviderPass())
            ->compile();

        $commandDispatcher = $container->get('command_dispatcher');
        $queryDispatcher = $container->get('query_dispatcher');
        $this->assertInstanceOf(CommandDispatcher::class, $commandDispatcher);
        $this->assertInstanceOf(QueryDispatcher::class, $queryDispatcher);
    }

    private function makeContainer(string $config, string $alias = null): SymfonyContainerBuilder
    {
        $container = new SymfonyContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load($config);
        $container->addCompilerPass(new HandlerProviderPass());

        if ($alias) {
            $container->setAlias($alias, new Alias(HandlerProvider::class, true));
        }

        $container->compile();

        return $container;
    }
}
