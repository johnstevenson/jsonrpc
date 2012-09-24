Json-Rpc
========

[![Build Status](https://secure.travis-ci.org/johnstevenson/json-rpc.png)](http://travis-ci.org/johnstevenson/json-rpc)

About
-----

A PHP implementation for JSON-RPC (v2). Contains client and server libraries to handle **call**, **notify** and **batch** requests.

JSON-RPC  is a protocol that allows servers to talk to each other using json-encoded structures. It is described in its specification as:

> A light weight remote procedure call protocol. It is designed to be simple!

Full details at [jsonrpc.org][json-spec]. You may need to read this to get an overview of the json structures that are used, although the specific implementation details are abstracted away by this package.

To call a method on a remote server is as simple as:

```php
<?php

$Client = new JsonRpc\Client($url);
$Client->call('method', array($param1, $param2));

// now do something with $Client->result
```

And at the server end:

```php
<?php

// MethodsClass contains the exposed methods
$methods = new MethodsClass();

$Server = new JsonRpc\Server($methods);
$Server->receive();

// and that's it, the library handles everything
```

License
-------

Json-Rpc is licensed under the MIT License - see the `LICENSE` file for details


  [json-spec]: http://www.jsonrpc.org/
