# MessageBus

[![Packagist](https://img.shields.io/packagist/v/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/MessageBus.svg)](https://scrutinizer-ci.com/g/ICanBoogie/MessageBus)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/MessageBus.svg)](https://coveralls.io/r/ICanBoogie/MessageBus)
[![Downloads](https://img.shields.io/packagist/dt/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus)

A message dispatcher helps to separate presentation concerns from business logic by mapping inputs
of various sources to simpler application messages. It also helps to decouple the domain from the
implementation, for an application only has to know about the messages, not how they are handled. A
design well known in [Hexagonal architectures][hexagonal].

**ICanBoogie/MessageBus** provides an implementation of a message dispatcher, with support for
permissions and voters. There's also a simple implementation of a message handler provider, and one
more sophisticated that works with [PSR-11][] containers. Finally, there's special support for
[Symfony's Dependency Injection component][symfony/di].

Using a message dispatcher can be as simple as the following example:

```php
<?php

namespace ICanBoogie\MessageBus;

/* @var Dispatcher $dispatcher */
/* @var object $message */

// The message is dispatched to its handler, the result is returned.
$result = $dispatcher->dispatch($message);
```



#### Installation

```bash
composer require icanboogie/message-bus
```

If you're upgrading to a newer version, please check the [Migration guide](MIGRATION.md).



## Messages

Messages can be any type of object. What's important is that all input type considerations (e.g.
HTTP details) are removed to keep what's essential for the domain. That is, a controller would
create a message to dispatch, but controller concern would stay in the controller, they would not
leak into the message.

The following example demonstrates how a `DeleteMenu` message could be defined. Note that there
isn't a notion of input type or authorization. These are presentation concerns, and they should
remain there.

```php
<?php

// …

final class DeleteMenu
{
    public function __construct(
        public /*readonly*/ int $id
    ) {
    }
}
```

Messages that don't alter the state of an application—in other words, messages that lead to
read-only operations—can be marked with the `Safe` interface. This is not a requirement, just a
recommendation to help you identify messages.

```php
<?php

use ICanBoogie\MessageBus\Safe;

// …

final class ShowMenu implements Safe
{
    public function __construct(
        public /*readonly*/ int $id
    ) {
    }
}
```



## Message handlers

Message handlers handle messages. Usually the relation is 1:1, that is one handler for one message
type. Message handlers are callables, typically a class implementing `__invoke(T $message)`, where
`T` is a type of message.

The following example demonstrates how a handler can be defined for a `ShowMenu` message:

```php
<?php

final class ShowMenuHandler
{
    public function __invoke(ShowMenu $message): Menu
    {
        // …
    }
}
```




## Providing message handlers

Message handlers are obtained through a provider, usually backed by a service container.

The following example demonstrates how to obtain a message handler for a given message, and how to
invoke that handler to get a result.

```php
<?php

/* @var ICanBoogie\MessageBus\HandlerProvider $provider */
/* @var object $message */

$handler = $provider->getHandlerForMessage($message);
$result = $handler($message);
```





### Providing handlers with a PSR container

Handlers can be provided by an instance of [PSR\HandlerProviderWithContainer][], which is backed by
a [PSR container][PSR-11]. You need to provide the mapping from "message class" to "handler service
identifier".

```php
<?php

use ICanBoogie\MessageBus\PSR\HandlerProviderWithContainer;
use Psr\Container\ContainerInterface;

/* @var $container ContainerInterface */

$handlerProvider = new HandlerProviderWithContainer($container, [

	Acme\MenuService\Application\MessageBus\CreateMenu::class => Acme\MenuService\Application\MessageBus\CreateMenuHandler::class,
	Acme\MenuService\Application\MessageBus\DeleteMenu::class => Acme\MenuService\Application\MessageBus\DeleteMenuHandler::class,

]);
```

### Providing handlers with Symfony's Dependency Injection component

The easiest way to define messages, handlers, permissions, and voters, is with [Symfony's Dependency
Injection][symfony/di] component. The handlers are defined as services, tags are used to identify
them and the message type they support. It's also possible to define permissions, and voter for
those permissions.

You can use the [`services.yaml`][] file provided directly in your project, together with the
compiler pass [MessageBusPass][].

The following example demonstrates how to define handlers, commands, permission, and voters:

```yaml
services:
  Acme\MenuService\Application\MessageBus\CreateMenuHandler:
    tags:
    - { name: message_bus.handler, message: Acme\MenuService\Application\MessageBus\CreateMenu }
    - { name: message_bus.permission, permission: is_admin }
    - { name: message_bus.permission, permission: can_write_menu }

  Acme\MenuService\Application\MessageBus\DeleteMenuHandler:
    tags:
    - { name: message_bus.handler, message: Acme\MenuService\Application\MessageBus\DeleteMenu }
    - { name: message_bus.permission, permission: is_admin }
    - { name: message_bus.permission, permission: can_manage_menu }

  Acme\MenuService\Presentation\Security\Voters\IsAdmin:
    tags:
    - { name: message_bus.voter, permission: is_admin }

  Acme\MenuService\Presentation\Security\Voters\CanWriteMenu:
    tags:
    - { name: message_bus.voter, permission: can_write_menu }

  Acme\MenuService\Presentation\Security\Voters\CanManageMenu:
    tags:
    - { name: message_bus.voter, permission: can_manage_menu }
```

```php
<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use ICanBoogie\MessageBus\Symfony\MessageBusPass;

/* @var string $config */

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load($config);
$container->addCompilerPass(new MessageBusPass());
$container->compile();
```





#### Using PHP 8 attributes instead of YAML

With the [composer-attribute-collector][] [Composer][] plugin, PHP 8 attributes can be used instead
of YAML to define handlers, permissions, and voters.

For handlers and permissions, the [Handler](lib/Attribute/Handler.php) and
[Permission](lib/Attribute/Permission.php) attributes can be used to replace YAML:

```php

namespace Acme\MenuService\Application\MessageBus;

use ICanBoogie\MessageBus\Attribute\Handler;
use ICanBoogie\MessageBus\Attribute\Permission;

#[Permission('is_admin')]
#[Permission('can_write_menu')]
final class CreateMenu
{
    // ...
}

#[Handler]
final class CreateMenuHandler
{
    // ...
}
```

For voters, the [Vote](lib/Attribute/Vote.php) attribute can be used to replace YAML:

```php
<?php

namespace Acme\MenuService\Presentation\Security\Voters;

use ICanBoogie\MessageBus\Attribute\Vote;

#[Vote('can_write_menu')]
final class CanWriteMenu
{
    // ...
}
```

```yaml
  Acme\MenuService\Application\MessageBus\CreateMenuHandler:
    tags:
    - { name: message_bus.handler, message: Acme\MenuService\Application\MessageBus\CreateMenu }
    - { name: message_bus.permission, permission: is_admin }
    - { name: message_bus.permission, permission: can_write_menu }
```

You just need to add the compiler pass [MessagePubPassWithAttributes](lib/Symfony/MessageBusPassWithAttributes.php)
before [MessageBusPass](lib/Symfony/MessageBusPass.php):

```php
<?php

// ...
$container->addCompilerPass(new MessageBusPassWithAttributes());
$container->addCompilerPass(new MessageBusPass());
// ...
```





### Providing handlers using a chain of providers

With `HandlerProviderWithChain` you can chain multiple handler providers together. They will be used
in sequence until a handler is found.

```php
<?php

namespace ICanBoogie\MessageBus;

/* @var HandlerProviderWithHandlers $providerWithHandlers */
/* @var PSR\HandlerProviderWithContainer $providerWithContainer */

$provider = new HandlerProviderWithChain([ $providerWithHandlers, $providerWithContainer ]);

/* @var object $message */

$handler = $provider->getHandlerForMessage($message);
```


## Permissions and voters

You probably want to restrict the dispatch of messages to certain conditions. For example, deleting
records should only be possible for users having a certain scope in their [JWT][]. For this, you
want to make sure of a few things:

1. Define voters and the permission they vote for.

    ```yaml
    services:
      Acme\MenuService\Presentation\Security\Voters\CanManageMenu:
        tags:
        - { name: message_bus.voter, permission: can_manage_menu }
    ```

2. Tag the permissions together with the handler and message definition.

    ```yaml
    services:
      Acme\MenuService\Application\MessageBus\DeleteMenuHandler:
        tags:
        - { name: message_bus.handler, message: Acme\MenuService\Application\MessageBus\DeleteMenu }
        - { name: message_bus.permission, permission: can_manage_menu }
    ```

3. Require a [RestrictedDispatcher][] instead of a [Dispatcher][].

    ```php
    <?php

    // ...

    use ICanBoogie\MessageBus\RestrictedDispatcher;

    final class MenuController
    {
        public function __construct(
            private RestrictedDispatcher $dispatcher
        ) {}
    }
    ```

4. Put in the context whatever is required for the voters to make their decision. In the following
example, that would be a token, for the voter to check for scopes.

    ```php
    <?php

    // ...

    use ICanBoogie\MessageBus\Context;

    final class MenuController
    {
        // ...

        public function delete(Request $request): Response
        {
            // ...

            $this->dispatch(
                new MenuDelete($id),
                new Context([ $token ])
            );
        }
    }
    ```



----------



## Continuous Integration

The project is continuously tested by [GitHub actions](https://github.com/ICanBoogie/MessageBus/actions).

[![Tests](https://github.com/ICanBoogie/MessageBus/workflows/test/badge.svg?branch=master)](https://github.com/ICanBoogie/MessageBus/actions?query=workflow%3Atest)
[![Static Analysis](https://github.com/ICanBoogie/MessageBus/workflows/static-analysis/badge.svg?branch=master)](https://github.com/ICanBoogie/MessageBus/actions?query=workflow%3Astatic-analysis)
[![Code Style](https://github.com/ICanBoogie/MessageBus/workflows/code-style/badge.svg?branch=master)](https://github.com/ICanBoogie/MessageBus/actions?query=workflow%3Acode-style)



## Code of Conduct

This project adheres to a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in
this project and its community, you are expected to uphold this code.



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.



## License

**icanboogie/message-bus** is released under the [BSD-3-Clause](LICENSE).



[Composer]:                            https://getcomposer.org/
[composer-attribute-collector]:        https://github.com/olvlvl/composer-attribute-collector/
[ICanBoogie]:                          https://icanboogie.org/
[JWT]:                                 https://jwt.io/
[symfony/dependency-injection]:        https://symfony.com/doc/current/components/dependency_injection.html
[hexagonal]:                           https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/
[PSR-11]:                              https://www.php-fig.org/psr/psr-11/
[symfony/di]:                          https://symfony.com/doc/current/components/dependency_injection.html

[`services.yaml`]:                     lib/Symfony/services.yaml
[Dispatcher]:                          lib/Dispatcher.php
[HandlerProvider]:                     lib/HandlerProvider.php
[MessageBusPass]:                      lib/Symfony/MessageBusPass.php
[PSR\HandlerProviderWithContainer]:    lib/PSR/HandlerProviderWithContainer.php
[RestrictedDispatcher]:                lib/RestrictedDispatcher.php
