#json-rpc

A JSON-RPC implementation for PHP using version 2 of the specification. Contains client and server libraries to handle call, notify and batch requests.

JSON-RPC  is a protocol that allows servers to talk to each other using json-encoded structures which, according to the specification, is:

> A light weight remote procedure call protocol. It is designed to be simple!

You can read more at [jsonrpc.org][json-spec] to get an overview of the structures that are used, although the specific implementation details are abstracted away by this package.

To call a function on a remote server is as simple as:

    $Client = new JsonRpc\Client($url);
    $result = $client->call('method', array($param1, $param2));

To process functions on a remote server:

      $functionHandler = new ApiFunctions();
      $Server = new JsonRpc\Server($functionHandler);
      $Server->receive();




  [json-spec]: http://www.jsonrpc.org/
