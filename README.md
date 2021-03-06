# MessageBus

[![Packagist](https://img.shields.io/packagist/v/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus)
[![Build Status](https://img.shields.io/github/workflow/status/ICanBoogie/MessageBus/test)](https://github.com/ICanBoogie/MessageBus/actions?query=workflow%3Atest)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/MessageBus.svg)](https://scrutinizer-ci.com/g/ICanBoogie/MessageBus)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/MessageBus.svg)](https://coveralls.io/r/ICanBoogie/MessageBus)
[![Downloads](https://img.shields.io/packagist/dt/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus/stats)

A message dispatcher helps to separate presentation concerns from business logic by mapping inputs
of various sources to simpler application messages. It also helps to decouple the domain from the
implementation, for an application only has to know about the messages, not how they are handled. A
design well know in [Hexagonal architectures][hexagonal].

Going further, and following the [Command–query separation][cqs] principle, every message should
either be a _command_ that performs an action, or a _query_ that returns data to the caller, but not
both.

**ICanBoogie/MessageBus** provides a straightforward implementation of a message dispatcher, and
going further of a command dispatcher and a query dispatcher. There's also a simple implementation
of a message handler provider, and one more sophisticated that works with [PSR-11][] containers.
Finally, there's special support for [Symfony's Dependency Injection component][symfony/di].

Using a message dispatcher can be as simple as the following example:

```php
<?php

/* @var ICanBoogie\MessageBus\Dispatcher $dispatcher */
/* @var object $message */

// The message is dispatched to its handler, the result is returned.
$result = $dispatcher->dispatch($message);
```





## Message handlers

A handler needs to be specified for each message type. Usually the relation is 1:1, be it's not
uncommon to have a same handler class handling different similar messages e.g. `DeleteMenu`,
`DeleteRecipe`…

The dispatcher is agnostic about the message/handler mapping and retrieves the handler through the
[HandlerProvider][] interface, as demonstrated in the following example:

```php

/* @var ICanBoogie\MessageBus\HandlerProvider $provider */
/* @var object $message */

$result = $provider->getHandlerForMessage($message)($message);
```





### Providing handlers

The package includes a simple handler provider that only requires an array of key/value pairs, but
more sophisticated ones are also available.

The following example demonstrates how to define a message handler provider with a selection
of messages and their handlers:

```php
<?php

use ACME\Application\Command;
use ACME\Application\Query;
use ICanBoogie\MessageBus\SimpleHandlerProvider;

$handlerProvider = new SimpleHandlerProvider([

	Command\CreateArticle::class => function (Command\CreateArticle $message) {

		// create an article

	},

	Query\ShowArticle::class => function (Query\ShowArticle $message) {

        // show an article

    },

]);
```





### Providing handlers with a PSR container

Use an instance of [PSR\ContainerHandlerProvider][] to provide handlers from a
[PSR container][PSR-11]:

```php
<?php

use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;

/* @var $container \Psr\Container\ContainerInterface */

$handlerProvider = new ContainerHandlerProvider($container, [

	ACME\Application\Command\CreateArticle::class => 'handler.article.create',
	ACME\Application\Query\ShowArticle::class => 'handler.article.show',

]);
```

### Using Symfony's Dependency Injection component

The easiest way to define commands, queries, and their handler is with [Symfony's Dependency
Injection][symfony/di] component. The handlers are defined as services, tags are used to identify
command/query handlers and the comman/query they support. Compiler passes collect the services and
create a command dispatcher and a query dispatcher. The compiler passes come with sensible defaults,
but of course, you can configure all of this to your liking.

The following example demonstrates how to define handlers. There's a command handler and a query
handler, you can notice the use of `command_dispatcher.handler` and `query_dispatcher.handler`.

```yaml
services:
  handler.article.create:
    class: ACME\Application\Command\CreateArticleHandler
    tags:
      - name: command_dispatcher.handler
        command: ACME\Application\Command\CreateArticle

  handler.article.show:
    class: ACME\Application\Query\ShowArticleHandler
    tags:
      - name: query_dispatcher.handler
        query: ACME\Application\Query\ShowArticle
```

Simply add `CommandHandlerProviderPass` and `QueryHandlerProviderPass` to the compiler passes:

```php
<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use ICanBoogie\MessageBus\Symfony\CommandHandlerProviderPass;
use ICanBoogie\MessageBus\Symfony\QueryHandlerProviderPass;

/* @var string $config */

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load($config);
$container->addCompilerPass(new CommandHandlerProviderPass());
$container->addCompilerPass(new QueryHandlerProviderPass());
$container->compile();
```

Request `CommandDispatcher` or `QueryDispatcher` in your constructor to get one or the other.





## Asserting messages before dispatching

The [AssertingDispatcher][] decorator can be used to assert messages before they are dispatched. One
could use the assertion to reject messages that require special permissions.

```php
<?php

use ICanBoogie\MessageBus\AssertingDispatcher;

/* @var ICanBoogie\MessageBus\Dispatcher $dispatcher */

$assertingDispatcher = new AssertingDispatcher($dispatcher, function ($message) {

	if (/* some condition */)
		throw new \LogicException("The message should not be dispatched.");

});
```





----------





## Requirements

The package requires PHP 7.2 or later.





## Installation

```bash
composer require icanboogie/message-bus
```





## Testing

Run `make test-container` to create and log into the test container, then run `make test` to run the
test suite. Alternatively, run `make test-coverage` to run the test suite with test coverage. Open
`build/coverage/index.html` to see the breakdown of the code coverage.





## License

**icanboogie/message-bus** is released under the [New BSD License](LICENSE).





[AssertingDispatcher]:                 lib/AssertingDispatcher.php
[HandlerProvider]:                     lib/HandlerProvider.php
[HandlerProviderPass]:                 lib/Symfony/HandlerProviderPass.php
[PSR\ContainerHandlerProvider]:        lib/PSR/ContainerHandlerProvider.php
[available on GitHub]:                 https://github.com/ICanBoogie/MessageBus
[ICanBoogie]:                          https://icanboogie.org
[symfony/dependency-injection]:        https://symfony.com/doc/current/components/dependency_injection.html
[hexagonal]:                           https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/
[cqs]:                                 https://en.wikipedia.org/wiki/Command%E2%80%93query_separation
[PSR-11]:                              https://www.php-fig.org/psr/psr-11/
[symfony/di]:                          https://symfony.com/doc/current/components/dependency_injection.html
