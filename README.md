# MessageBus

[![Packagist](https://img.shields.io/packagist/v/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus)
[![Build Status](https://img.shields.io/travis/ICanBoogie/MessageBus.svg)](http://travis-ci.org/ICanBoogie/MessageBus)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/MessageBus.svg)](https://scrutinizer-ci.com/g/ICanBoogie/MessageBus)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/MessageBus.svg)](https://coveralls.io/r/ICanBoogie/MessageBus)
[![Downloads](https://img.shields.io/packagist/dt/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus/stats)

**ICanBoogie/MessageBus** provides a very simple message dispatcher. Implemented with a functional
approach, it tries to be as flexible as possible: the handler provider is defined as a simple
callables that you can implement or decorate with your favorite resolver.

```php
<?php

namespace ICanBoogie\MessageBus;

/* @var HandlerProvider|callable $handler_provider */

$dispatcher = new SimpleDispatcher($handler_provider);

/* @var object $message */

// The message is dispatched by an handler
$result = $dispatcher->dispatch($message);

/* @var callable $assertion */

$asserting_dispatcher = new AssertingDispatcher($dispatcher, $assertion);
```





## Message handler provider

The message handler provider is a callable with a signature similar to the
[HandlerProvider][] interface, the package provides a simple message handler provider
that only requires an array of key/value pairs, where _key_ is a message class and _value_
a message handler callable.





### Providing handlers

The following example demonstrates how to define a message handler provider with a selection
of messages and their handlers:

```php
<?php

use App\Application\Message;
use ICanBoogie\MessageBus\SimpleHandlerProvider;

$handler_provider = new SimpleHandlerProvider([

	Message\CreateArticle::class => function (Message\CreateArticle $message) {

		// create an article

	},

	Message\DeleteArticle::class => function (Message\DeleteArticle $message) {

        // delete an article

    },

]);
```





### Providing handlers with icanboogie/service

Of course, if you're using the [icanboogie/service][] package, you can use service references
instead of callables (well, technically, they are also callables):

```php
<?php

use App\Application\Message;
use ICanBoogie\MessageBus\SimpleHandlerProvider;

use function ICanBoogie\Service\ref;

$handler_provider = new SimpleHandlerProvider([

	Message\CreateArticle::class => ref('handler.article.create'),
	Message\DeleteArticle::class => ref('handler.article.delete'),

]);
```




### Providing handlers with a PSR container

Use an instance of [PSR\ContainerHandlerProvider][] to provide handlers from a [PSR container][]:

```php
<?php

use App\Application\Message;
use ICanBoogie\MessageBus\PSR\ContainerHandlerProvider;

/* @var $container \Psr\Container\ContainerInterface */

$handler_provider = new ContainerHandlerProvider([

	Message\CreateArticle::class => 'handler.article.create',
	Message\DeleteArticle::class => 'handler.article.delete',

], $container);
```

If you're using [symfony/dependency-injection][] you can add an instance of [MessageBusPass][]
to your compilation pass to automatically generate the provider:

```yaml
services:
  handler.article.create:
    class: App\Domain\Article\Handler\CreateArticleHandler
    tags:
      - name: message_bus.handler
        message: App\Application\Message\CreateArticle
```

```php
<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use ICanBoogie\MessageBus\Symfony\MessageBusPass;

/* @var string $config */
/* @var ICanBoogie\MessageBus\PSR\ContainerHandlerProvider $provider */

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load($config);
$container->addCompilerPass(new MessageBusPass);
$container->compile();

$provider = $container->get(MessageBusPass::DEFAULT_PROVIDER_SERVICE);
```





## Asserting messages before dispatching

The [AssertingDispatcher][] decorator can be used to assert messages before they are dispatched. One
could use the assertion to reject messages that require special permissions.

```php
<?php

use ICanBoogie\MessageBus\AssertingDispatcher;

/* @var ICanBoogie\MessageBus\Dispatcher $dispatcher */

$asserting_dispatcher = new AssertingDispatcher($dispatcher, function ($message) {

	if (/* some condition */)
		throw new \LogicException("The message should not be dispatched.");

});
```





----------





## Requirements

The package requires PHP 5.6 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

	$ composer require icanboogie/message-bus





### Cloning the repository

The package is [available on GitHub][], its repository can be cloned with the following Message
line:

	$ git clone https://github.com/ICanBoogie/MessageBus.git





## Documentation

The package is documented as part of the [ICanBoogie][] framework [documentation][]. You can
generate the documentation for the package and its dependencies with the `make doc` Message. The
documentation is generated in the `build/docs` directory. [ApiGen](http://apigen.org/) is required.
The directory can later be cleaned with the `make clean` Message.





## Testing

The test suite is ran with the `make test` Message. [PHPUnit](https://phpunit.de/) and
[Composer](http://getcomposer.org/) need to be globally available to run the suite. The Message
installs dependencies as required. The `make test-coverage` Message runs test suite and also creates
an HTML coverage report in `build/coverage`. The directory can later be cleaned with the `make
clean` Message.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/ICanBoogie/MessageBus.svg)](http://travis-ci.org/ICanBoogie/MessageBus)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/MessageBus.svg)](https://coveralls.io/r/ICanBoogie/MessageBus)





## License

**icanboogie/message-bus** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[documentation]:                       https://icanboogie.org/api/message-bus/master/
[AssertingDispatcher]:                 https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.AssertingDispatcher.html
[HandlerProvider]:                     https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.HandlerProvider.html
[MessageBusPass]:                      https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.Symfony.MessageBusPass.html
[available on GitHub]:                 https://github.com/ICanBoogie/MessageBus
[icanboogie/service]:                  https://github.com/ICanBoogie/Service
[PSR container]:                       https://github.com/php-fig/container
[ICanBoogie]:                          https://icanboogie.org
[PSR\ContainerHandlerProvider]:        https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.PSR.ContainerHandlerProvider.html
[symfony/dependency-injection]:        https://symfony.com/doc/current/components/dependency_injection.html
