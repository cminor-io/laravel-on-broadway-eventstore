# LaravelStore - LaravelOnBroadway Component

The LaravelStore Component provides an implementation of [Broadway]'s EventStore using laravel's native database driver, in comparison with the default implementation that uses Doctrive/DBAL adapter.

The component is part of the cminor.io/laravel-on-broadway package.

## Installation

Use composer.

`composer require cminor.io/laravel-on-broadway-eventstore`

## Usage

The LaravelStore has 3 dependencies:
- A laravel database connection (ConnectionInterface)
- A stream serializer (StreamSerializerInterface - included in the package)
- A table name

Examples of instantiation will come in the future. For now check the tests to see how the store is instantiated.

### Disclaimer

This package is still under development. 
Use this with common sense and at your own risk. I will try my best to fix any possible bugs. :)
Any help is always appreciated.

## Testing

The implementation uses the exact same tests as the rest of the Broadway's event store implementations to ensure compatibility.

Run the tests by issuing `./bin/phpunit`.


[Broadway]: https://github.com/qandidate-labs/broadway
