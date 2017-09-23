# MessageBus

[![Packagist](https://img.shields.io/packagist/v/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus)
[![Build Status](https://img.shields.io/travis/ICanBoogie/MessageBus.svg)](http://travis-ci.org/ICanBoogie/MessageBus)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/MessageBus.svg)](https://scrutinizer-ci.com/g/ICanBoogie/MessageBus)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/MessageBus.svg)](https://coveralls.io/r/ICanBoogie/MessageBus)
[![Downloads](https://img.shields.io/packagist/dt/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus/stats)

**ICanBoogie/MessageBus** provides a very simple message bus that can handle messages right away or
push them in a queue. Implemented with a functional approach, it tries to be as flexible as
possible: the message handler provider and the message pusher are defined with simple callables that
you can implement or decorate with your favorite resolver and your favorite message queue.

The following example demonstrates how to instantiate a message bus and dispatch messages. Messages
implementing the [ShouldBePushed][] interface are pushed to the queue rather than executed right
away.

```php
<?php

namespace ICanBoogie\MessageBus;

/* @var MessageHandlerProvider|callable $message_handler_provider */
/* @var MessagePusher|callable $message_pusher */

$bus = new SimpleMessageBus($message_handler_provider, $message_pusher);

/* @var object $message */

// The message is handled right away by an handler
$result = $bus->dispatch($message);

/* @var ShouldBePushed $message */

// The message is pushed to a queue
$bus->dispatch($message);
```





## Message handler provider

The message handler provider is a callable with a signature similar to the
[MessageHandlerProvider][] interface, the package provides a simple message handler provider
that only requires an array of key/value pairs, where _key_ is a message class and _value_
a message handler callable.





### Providing handlers

The following example demonstrates how to define a message handler provider with a selection
of messages and their handlers:

```php
<?php

use App\Application\Message;
use ICanBoogie\MessageBus\SimpleMessageHandlerProvider;

$message_handler_provider = new SimpleMessageHandlerProvider([

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
use ICanBoogie\MessageBus\SimpleMessageHandlerProvider;

use function ICanBoogie\Service\ref;

$message_handler_provider = new SimpleMessageHandlerProvider([

	Message\CreateArticle::class => ref('handler.article.create'),
	Message\DeleteArticle::class => ref('handler.article.delete'),

]);
```




### Providing handlers with a PSR container

Use an instance of [PSR\ContainerMessageHandlerProvider][] to provide handlers from a 
[PSR container][]:

```php
<?php

use App\Application\Message;
use ICanBoogie\MessageBus\PSR\ContainerMessageHandlerProvider;

/* @var $container \Psr\Container\ContainerInterface */

$message_handler_provider = new ContainerMessageHandlerProvider([

	Message\CreateArticle::class => 'handler.article.create',
	Message\DeleteArticle::class => 'handler.article.delete',

], $container);
```

If you're using [symfony/dependency-injection][] you can add an instance of [AddCommandBusPass][]
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
use ICanBoogie\MessageBus\Symfony\AddCommandBusPass;

/* @var string $config */
/* @var ICanBoogie\MessageBus\PSR\ContainerMessageHandlerProvider $provider */

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load($config);
$container->addCompilerPass(new AddCommandBusPass);
$container->compile();

$provider = $container->get(AddCommandBusPass::DEFAULT_PROVIDER_SERVICE);
```





## Asserting messages before dispatching

The [AssertingMessageBus][] decorator can be used to assert messages before they are dispatched. One
could use the assertion to reject messages that require special permissions.

```php
<?php

use ICanBoogie\MessageBus\AssertingMessageBus;

/* @var ICanBoogie\MessageBus\MessageBus $message_bus */

$asserting_message_bus = new AssertingMessageBus($message_bus, function ($message) {

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
[AssertingMessageBus]:                 https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.AssertingMessageBus.html
[MessageHandlerProvider]:              https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.MessageHandlerProvider.html
[ShouldBePushed]:                      https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.ShouldBePushed.html
[AddCommandBusPass]:                   https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.Symfony.AddCommandBusPass.html
[available on GitHub]:                 https://github.com/ICanBoogie/MessageBus
[icanboogie/service]:                  https://github.com/ICanBoogie/Service
[PSR container]:                       https://github.com/php-fig/container
[ICanBoogie]:                          https://icanboogie.org
[PSR\ContainerMessageHandlerProvider]: https://icanboogie.org/api/message-bus/master/class-ICanBoogie.MessageBus.PSR.ContainerMessageHandlerProvider.html
[symfony/dependency-injection]:        https://symfony.com/doc/current/components/dependency_injection.html
