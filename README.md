# Blablacar Redis Wrapper

[![Build Status](https://travis-ci.org/blablacar/redis-client.png)](https://travis-ci.org/blablacar/redis-client)

This lib provide a simple Redis connection wrapper.

## Installation

The recommended way to install this lib is through
[Composer](http://getcomposer.org/). Require the `blablcar/redis-client` package
into your `composer.json` file:

```json
{
    "require": {
        "blablacar/redis-client": "@stable"
    }
}
```

**Protip:** you should browse the
[`blablacar/redis-client`](https://packagist.org/packages/blablacar/redis-client)
page to choose a stable version to use, avoid the `@stable` meta constraint.

## Usage

Create a Client and you're down !

```php
$client = new \Blablacar\Redis\Client('127.0.0.1', 6379); // Default values
$client->set('foobar', 42); // Return 1
```

For more informations about Redis extension see the
[nicolasff/phpredis](https://github.com/nicolasff/phpredis).

## License

Blablacar Redis client is released under the MIT License. See the bundled
LICENSE file for details.
