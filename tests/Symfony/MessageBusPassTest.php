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

use Exception;
use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;
use ICanBoogie\MessageBus\RestrictedDispatcher;
use ICanBoogie\MessageBus\SimpleDispatcher;
use ICanBoogie\MessageBus\VoterProvider;
use ICanBoogie\MessageBus\VoterWithPermissions;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class MessageBusPassTest extends ContainerTestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->makeContainer(
            __DIR__ . '/resources/integration.yml',
            function (ContainerBuilder $container) {
                $container->addCompilerPass(new MessageBusPass());
            }
        );
    }

    /**
     * @dataProvider provideService
     *
     * @param class-string $expected
     *
     * @throws Exception
     */
    public function testService(string $id, string $expected): void
    {
        $this->assertInstanceOf($expected, $this->container->get($id));
    }

    // @phpstan-ignore-next-line
    public function provideService(): array
    {
        return [

            [ 'test.voter_provider', VoterProvider::class ],
            [ 'test.voter_with_permissions', VoterWithPermissions::class ],
            [ 'test.handler_provider', ContainerHandlerProvider::class ],
            [ 'test.dispatcher', SimpleDispatcher::class ],
            [ 'test.restricted_dispatcher', RestrictedDispatcher::class ],

        ];
    }

    /**
     * @dataProvider provideParameter
     */
    public function testParameter(string $name, mixed $expected): void
    {
        $this->assertSame($expected, $this->container->getParameter($name));
    }

    // @phpstan-ignore-next-line
    public function provideParameter(): array
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
}
