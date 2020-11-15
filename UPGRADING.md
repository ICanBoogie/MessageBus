# MessageBus Upgrade Guide

## 0.6-0.7 to 1.0

The biggest change in this release is the signature of the `HandlerProvider` interface. If you
implemented this interface, you'll have to change `__invoke(object $message)` to
`getHandlerForMessage(object $message): callable`. The change was inspired by [PSR-14][] and its
`ListenerProviderInterface` interface.

The exception `NoHandlerForMessage` was renamed as `NotFound`, if you were catching that exception,
you'll have to update.

Other changes are mostly under the hood and shouldn't be noticed.

### Breaking changes

- Changed `HandlerProvider::__invoke(object $message)` to `getHandlerForMessage(object $message): callable`.
- `ContainerHandlerProvider` expects container first, mapping second.
- `ContainerHandlerProvider` not longer extends `SimpleHandlerProvider`.
- Renamed `HandlerProviderPass::DEFAULT_QUERY_PROPERTY` as `DEFAULT_MESSAGE_PROPERTY`.
- Reworked `NoHandlerForMessage` and renamed as `NotFound`.
- Removed `Handler` interface.

### Other changes

- Added `HandlerProviderPass::DEFAULT_PROVIDER_CLASS`.
- Simplified `CommandHandlerProviderPass` and `QueryHandlerProviderPass`.
- Simplified `SimpleDispatcher`.
- `Exception` extends `Throwable`.




[PSR-14]: https://www.php-fig.org/psr/psr-14/
