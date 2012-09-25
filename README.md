# Json-Rpc

[![Build Status](https://secure.travis-ci.org/johnstevenson/json-rpc.png)](http://travis-ci.org/johnstevenson/json-rpc)

A PHP implementation for JSON-RPC (v2). Contains client and server libraries to handle requests including **notification** and **batch**.

About
-----

JSON-RPC  is a protocol that allows servers to talk to each other using json-encoded structures. It is described in its specification as:

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

Alternatively, you can [download][download] and extract it, or clone this repo.

## Usage
If you downloaded the library through [composer][composer] then you must add

```php
<?php
 require 'vendor/autoload.php';
```
somewhere in your bootstrap code, otherwise you must point a PSR-0 autoloader to the `src` directory so that the classes are automatically included.

If you just want to have a quick play, point your browser to `example/client.php` and everything will run automatically.

## Client usage
This library provides a very easy way to make Json-Rpc request calls, including **notification** and **batch** requests. For all cases you first need to instantiate a `JsonRpc\Client`, which you do by giving it the url you want to send your requests to:

```php
<?php
$client = new JsonRpc\Client($url);
```
### Requests

You send a request by using the `$client->call` function. This takes the name of the method you want to invoke on the server and its parameters, if it requires any. The only tricky bit here is remembering to put the parameters into an array, similar to PHP's `call_user_func_array` function.

```php
<?php
$success = $client->call('method', array($param1, $param2));
```

The function returns true or false. If **true** then the result of the method will be in the `$client->result` property. The type of the result depends on what has been returned by the server, so it could be a scalar, an indexed array, an object `stdClass`, or null.

If **false** then an error has occured, either in sending or processing the request. This is reported in the `$client->error` property, which is a string. Putting it all together:

```php
<?php
$client = new JsonRpc\Client($url);

if ($client->call('method', array($param1, $param2)))
{
  return $client->result;
}
else
{
  error_log($client->error);
}
```
If you just want a client to send simple requests, then there is no need to read any further. However the next sections explain notification and batch requests.

---

### Notification requests
A notification is a call to the server which does not require a response. You send one by using the `$client->notify` function, which takes the same arguments as the `$client->call` function described above:

```php
<?php
$success = $client->notify('method', array($param));
```

The function returns either **true**, meaning that the request was sent and received, or **false**, indicating that there was an error *sending* the request which will be reported in the `$client->error` property.

Note that there is no way of knowing if the server encountered an error while processing your request, for example if you supplied the wrong parameters. Such is the nature of notifications.

### Batch requests
A batch request is one which sends several requests at the same time, including notifications:

* call the `$client->batchOpen` function to start a batch operation
* populate the batch by making `$client->call` and `$client->notify` calls
* send the batch with the `$client->batchSend` function.

```php
<?php
$client->batchOpen();

$client->call('divide', array(42, 6));
$client->call('method2', array($param1, $param2));
$client->notify('update', array($param);
  ...
$success = $client->batchSend();
```

The function returns either **true**, meaning that the batch has been processed, or **false**, indicating that there was an error *sending* the batch request which will be reported in the `$client->error` property.

You have to do a bit more work to process the response from a batch operation, which will be in the `$client->batch` property. This is an indexed array of all the responses from the server, each being a `stdClass` object representing a result or an error. Using the example above and assuming that *method2* was not found on the server, this is what `$client->batch` looks like:

```
Array
(
    [0] => stdClass Object
        (
          [jsonrpc] => 2.0
          [result] => 7
          [id] => 1
        )

    [1] => stdClass Object
        (
          [jsonrpc] => 2.0
          [error] => stdClass Object
            (
              [code] => -32601
              [message] => Method not found
            )
          [id] => 2
        )
)
```
Note that there is no response to our notify call, because one is not returned, and that each response is assigned an `id` property, which is allocated with each `$client->call` ascending from `1`. The responses returned in the `$client->batch` array are similarly ordered (even though they may have been received out of sequence). Notifications do not have an `id`.

It is worth reading [the official specification][json-spec] if you want to make batch requests. Also the example included here at `example/client.php` is useful to experiment with.

If you have got this far, then you probably want to know how to set up your server to receive all the Json-Rpc requests you have just learned about. The next section covers this.

---

## Server usage


## License

Json-Rpc is licensed under the MIT License - see the `LICENSE` file for details


  [json-spec]: http://www.jsonrpc.org/
  [composer]: http://getcomposer.org
  [download]: https://github.com/johnstevenson/json-rpc/downloads
