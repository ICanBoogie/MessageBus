# Migration

## v2.x to v3.x

### New Requirements

None

### New features

None

### Backward Incompatible Changes

- `HandlerProvider::getHandlerForMessage()` can now return `null`, undefined handlers are handled in
  one single location now, that is `DispatcherWithHandlerProvider`.
- Dropped `HandlerProviderPass`, `CommandDispatcher`, `CommandHandlerProvider`,
  `CommandHandlerProviderPass`, `QueryDispatcher`, `QueryHandlerProvider`,
  `QueryHandlerProviderPass` in favor of `MessageBusPass`.
- Dropped `ContainerHandlerProvider` in favor of `HandlerProviderWithContainer`.
- Dropped `AssertingDispatcher` in favor of `RestrictedDispatcher`.
- Dropped `SimpleHandlerProvider` in favor of `HandlerProviderWithHandlers`.
- Dropped `SimpleDispatcher` in favor of `DispatcherWithHandlerProvider`.
- Dropped `NotFound` in favor of `HandlerNotFound`.

### Deprecated Features

None

### Other Changes

None



## v2.0 to v2.1

### New Requirements

None

### New features

- Add support for permissions and voters.
- Add `MessageBusPass` that takes care of handlers, commands, permissions, and voters. This time
  around the compiler pass generates parameters instead of fulfilled services, offering more
  flexibility in how they are used. Unlike `HandlerProviderPass`, `MessageBusPass` uses the tag
  `message_bus.handler` instead of `message_dispatcher.handler`.
- Add a `services.yaml` sample file, that can be used as is with Symfony's DIC.
- Add `Safe`, a way to identify safe messages.
- Add `DispatcherWithHandlerProvider`, a replacement for `SimpleDispatcher`.
- Add `HandlerProviderWithContainer`, a replacement for `ContainerHandlerProvider`.
- Add `HandlerProviderWithHandlers`, a replacement for `SimpleHandlerProvider`.
- Add `HandlerNotFound`, a replacement for `NotFound`.

### Backward Incompatible Changes

None

### Deprecated Features

- Deprecate `HandlerProviderPass`, `CommandDispatcher`, `CommandHandlerProvider`,
  `CommandHandlerProviderPass`, `QueryDispatcher`, `QueryHandlerProvider`,
  `QueryHandlerProviderPass` in favor of `MessageBusPass`.
- Deprecate `AssertingDispatcher` in favor of `RestrictedDispatcher`.
- Deprecate `SimpleDispatcher` in favor of `DispatcherWithHandlerProvider`.
- Deprecate `SimpleHandlerProvider` in favor of `HandlerProviderWithHandlers`.
- Deprecate `ContainerHandlerProvider` in favor of `HandlerProviderWithContainer`.
- Deprecate `NotFound` in favor of `HandlerNotFound`.

### Other Changes

None



## v1.x to v2.0

### New Requirements

- Require [PHP 8.0](https://www.php.net/releases/8.0/en.php)+
- Compatible with [psr/container](https://github.com/php-fig/container) 1.0 and 2.0
- Compatible with [Symfony 6.0](https://symfony.com/releases/6.0)

### New features

None

### Backward Incompatible Changes

None

### Deprecated Features

None

### Other Changes

None



## v0.6, v0.7 to v1.0

The biggest change in this release is the signature of the `HandlerProvider` interface. If you
implemented this interface, you'll have to change `__invoke(object $message)` to
`getHandlerForMessage(object $message): callable`. The change was inspired by [PSR-14][] and its
`ListenerProviderInterface` interface.

The exception `NoHandlerForMessage` was renamed as `NotFound`, if you were catching that exception,
you'll have to update.

Other changes are mostly under the hood and shouldn't be noticed.

### New Requirements

None

### New features

- Added `HandlerProviderPass::DEFAULT_PROVIDER_CLASS`.

### Backward Incompatible Changes

- Changed `HandlerProvider::__invoke(object $message)` to `getHandlerForMessage(object $message): callable`.
- `ContainerHandlerProvider` expects container first, mapping second.
- `ContainerHandlerProvider` not longer extends `SimpleHandlerProvider`.
- Renamed `HandlerProviderPass::DEFAULT_QUERY_PROPERTY` as `DEFAULT_MESSAGE_PROPERTY`.
- Reworked `NoHandlerForMessage` and renamed as `NotFound`.
- Removed `Handler` interface.

### Deprecated Features

None

### Other Changes

- Simplified `CommandHandlerProviderPass` and `QueryHandlerProviderPass`.
- Simplified `SimpleDispatcher`.
- `Exception` extends `Throwable`.
