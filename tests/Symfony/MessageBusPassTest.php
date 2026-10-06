<?php

namespace ICanBoogie\MessageBus\Symfony;

use Exception;
use ICanBoogie\MessageBus\DispatcherWithHandlerProvider;
use ICanBoogie\MessageBus\PSR\HandlerProviderWithContainer;
use ICanBoogie\MessageBus\RestrictedDispatcher;
use ICanBoogie\MessageBus\VoterProvider;
use ICanBoogie\MessageBus\VoterWithPermissions;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class MessageBusPassTest extends TestCase
{
    public function testFailOnDuplicateWithoutAttribute(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches("/already registered/");
        $this->makeContainer(
            __DIR__ . '/resources/message-duplicate.yml'
        );
    }

    public function testFailOnMissingMessage(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageIs("Missing attribute 'message' for service 'handler.message_a'");
        $this->makeContainer(
            __DIR__ . '/resources/missing-message.yml'
        );
    }

    /**
     * @param class-string $expected
     *
     * @throws Exception
     */
    #[DataProvider("provideService")]
    public function testService(string $id, string $expected): void
    {
        $container = $this->makeContainer(
            __DIR__ . '/resources/without-attributes/integration.yaml'
        );

        $this->assertInstanceOf($expected, $container->get($id));
    }

    /**
     * @param class-string $expected
     *
     * @throws Exception
     */
    #[DataProvider("provideService")]
    public function testServiceWithAttributes(string $id, string $expected): void
    {
        $container = $this->makeContainerWithAttributes(
            __DIR__ . '/resources/with-attributes/integration.yaml'
        );

        $this->assertInstanceOf($expected, $container->get($id));
    }

    // @phpstan-ignore-next-line
    public static function provideService(): array
    {
        return [

            [ 'test.voter_provider', VoterProvider::class ],
            [ 'test.voter_with_permissions', VoterWithPermissions::class ],
            [ 'test.handler_provider', HandlerProviderWithContainer::class ],
            [ 'test.dispatcher', DispatcherWithHandlerProvider::class ],
            [ 'test.restricted_dispatcher', RestrictedDispatcher::class ],

        ];
    }

    #[DataProvider("provideParameter")]
    public function testParameter(string $name, mixed $expected): void
    {
        $container = $this->makeContainer(
            __DIR__ . '/resources/without-attributes/integration.yaml'
        );

        $this->assertSame($expected, $container->getParameter($name));
    }

    #[DataProvider("provideParameter")]
    public function testParameterWithAttributes(string $name, mixed $expected): void
    {
        $container = $this->makeContainerWithAttributes(
            __DIR__ . '/resources/with-attributes/integration.yaml'
        );
        $actual = $container->getParameter($name);

        $this->assertEquals($expected, $actual);
    }

    // @phpstan-ignore-next-line
    public static function provideParameter(): array
    {
        return [

            [
                MessageBusPass::DEFAULT_PARAMETER_FOR_MESSAGE_TO_HANDLER,
                [
                    'Acme\MenuService\Application\MessageBus\CreateMenu'
                    => 'Acme\MenuService\Application\MessageBus\CreateMenuHandler',
                    'Acme\MenuService\Application\MessageBus\DeleteMenu'
                    => 'Acme\MenuService\Application\MessageBus\DeleteMenuHandler',
                ]
            ],

            [
                MessageBusPass::DEFAULT_PARAMETER_FOR_PERMISSIONS_BY_MESSAGE,
                [
                    'Acme\MenuService\Application\MessageBus\CreateMenu' => [ 'is_admin', 'can_write_menu' ],
                    'Acme\MenuService\Application\MessageBus\DeleteMenu' => [ 'is_admin', 'can_manage_menu' ],
                ]
            ],

            [
                MessageBusPass::DEFAULT_PARAMETER_FOR_PERMISSION_TO_VOTER,
                [
                    'is_admin' => 'Acme\MenuService\Presentation\Security\Voters\IsAdmin',
                    'can_write_menu' => 'Acme\MenuService\Presentation\Security\Voters\CanWriteMenu',
                    'can_manage_menu' => 'Acme\MenuService\Presentation\Security\Voters\CanManageMenu',
                ]
            ],

        ];
    }

    private function makeContainer(string $config, bool $withAttributes = false): SymfonyContainerBuilder
    {
        $container = new SymfonyContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load($config);

        if ($withAttributes) {
            $container->addCompilerPass(new MessageBusPassWithAttributes());
        }

        $container->addCompilerPass(new MessageBusPass());
        $container->compile();

        return $container;
    }

    private function makeContainerWithAttributes(string $config): SymfonyContainerBuilder
    {
        return $this->makeContainer($config, true);
    }
}
