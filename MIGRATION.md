# Migration

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
