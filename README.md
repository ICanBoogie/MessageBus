# MessageBus

[![Packagist](https://img.shields.io/packagist/v/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus)
[![Build Status](https://img.shields.io/travis/ICanBoogie/MessageBus/master.svg)](http://travis-ci.org/ICanBoogie/MessageBus)
[![HHVM](https://img.shields.io/hhvm/ICanBoogie/MessageBus.svg)](http://hhvm.h4cc.de/package/ICanBoogie/MessageBus)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/MessageBus/master.svg)](https://scrutinizer-ci.com/g/ICanBoogie/MessageBus)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/MessageBus/master.svg)](https://coveralls.io/r/ICanBoogie/MessageBus)
[![Downloads](https://img.shields.io/packagist/dt/icanboogie/message-bus.svg)](https://packagist.org/packages/icanboogie/message-bus/stats)

**ICanBoogie/MessageBus** provides a very simple message bus that can handle messages right away or
push them in a queue. Implemented with a functional approach, it tries to be as flexible as
possible: the message handler provider and the message pusher are defined with simple callables that
you can implement with your favorite resolver and your favorite message queue.

Three interfaces and a class make most of it: `MessageBus` defines a unique `dispatch()` method;
`Message` should be implemented by messages to handle, while `MessageToPush` by message to push in a
queue; finally the class `SimpleMessageBus` is a simple implementation of `MessageBus`.

The following example demonstrates how to instantiate a message bus and dispatch messages:

```php
<?php

namespace ICanBoogie\MessageBus;

/* @var MessageHandlerProvider|callable $message_handler_provider */
/* @var MessagePusher|callable $message_pusher */

$bus = new SimpleMessageBus($message_handler_provider, $message_pusher);

/* @var Message $message */

// The message is handled right away by an handler
$result = $bus->dispatch($message);

/* @var MessageToPush $message */

// The message is pushed to a queue
$bus->dispatch($message);
```





----------





## Requirements

The package requires PHP 5.6 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

```
$ composer require icanboogie/message-bus
```





### Cloning the repository

The package is [available on GitHub][], its repository can be cloned with the following command
line:

	$ git clone https://github.com/ICanBoogie/MessageBus.git





## Documentation

The package is documented as part of the [ICanBoogie][] framework [documentation][]. You can
generate the documentation for the package and its dependencies with the `make doc` command. The
documentation is generated in the `build/docs` directory. [ApiGen](http://apigen.org/) is required.
The directory can later be cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [PHPUnit](https://phpunit.de/) and
[Composer](http://getcomposer.org/) need to be globally available to run the suite. The command
installs dependencies as required. The `make test-coverage` command runs test suite and also creates
an HTML coverage report in `build/coverage`. The directory can later be cleaned with the `make
clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/ICanBoogie/MessageBus/master.svg)](http://travis-ci.org/ICanBoogie/MessageBus)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/MessageBus/master.svg)](https://coveralls.io/r/ICanBoogie/MessageBus)





## License

**icanboogie/message-bus** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[documentation]:                https://api.icanboogie.org/command-bus/latest/
[available on GitHub]:          https://github.com/ICanBoogie/MessageBus
[ICanBoogie]:                   https://icanboogie.org
