# Json-Rpc

[![Build Status](https://secure.travis-ci.org/johnstevenson/json-rpc.png)](http://travis-ci.org/johnstevenson/json-rpc)

A PHP implementation for JSON-RPC (v2). Contains client and server libraries to handle requests including **notification** and **batch**.

## Contents
* [About](#About)
* [Installation](#Installation)
* [Usage](#Usage)
* [Example](#Example)
* [License](#License)

<a name="About"></a>
## About

JSON-RPC is a protocol that allows servers to talk to each other using json-encoded structures. It is described in its specification as:

> A light weight remote procedure call protocol. It is designed to be simple!

Full details at [jsonrpc.org][json-spec]. You may need to read this to get an overview of the json structures that are used, although the heavy lifting is abstracted away by this implementation. For example, calling a method on a remote server is as simple as:

```php
<?php
$client = new JsonRpc\Client($url);
$client->call('method', array($param1, $param2));

// now do something with $client->result
```

And at the server end:

```php
<?php
// MethodsClass contains the exposed methods
$methods = new MethodsClass();

$server = new JsonRpc\Server($methods);
$server->receive();
```

<a name="Installation"></a>
## Installation
The easiest way is [through composer][composer]. Just create a `composer.json` file and run `php composer.phar install` to install it:

```json
{
    "minimum-stability": "dev",
    "require": {
        "jsonrpc/jsonrpc": "1.0.*"
    }
}
```

Alternatively, you can [download][download] and extract it, or clone this repo. If you just want to try it, see [Example](#Example).

<a name="Usage"></a>
## Usage
If you downloaded the library through [composer][composer] then you must add the following somewhere in your bootstrap code:

```php
<?php
 require 'vendor/autoload.php';
```
Otherwise you must point a PSR-0 autoloader to the `src` directory. Full usage [documentation][wiki] can be found in the Wiki:

* [Client usage][client]
* [Server usage][server]
* [Advanced functionality][advanced]

<a name="Example"></a>
## Example
The quickest way to get the library up and running locally is to point your browser to `example/client.php` and everything will load automatically. You can then experiment with the code as you read the documentation.

<a name="License"></a>
## License

Json-Rpc is licensed under the MIT License - see the `LICENSE` file for details


  [json-spec]: http://www.jsonrpc.org/
  [composer]: http://getcomposer.org
  [download]: https://github.com/johnstevenson/jsonrpc/downloads
  [wiki]:https://github.com/johnstevenson/jsonrpc/wiki/Home
  [client]:https://github.com/johnstevenson/jsonrpc/wiki/Client-usage
  [server]:https://github.com/johnstevenson/jsonrpc/wiki/Server-usage
  [advanced]:https://github.com/johnstevenson/jsonrpc/wiki/Advanced-functionality
