#json-rpc

A PHP implementation for JSON-RPC (v2). Contains client and server libraries to handle **call**, **notify** and **batch** requests.

JSON-RPC  is a protocol that allows servers to talk to each other using json-encoded structures. It is described in its specification as:

> A light weight remote procedure call protocol. It is designed to be simple!

Full details at [jsonrpc.org][json-spec]. You will need to read this to get an overview of the json structures that are used, although the specific implementation details are abstracted away by this package.

To call a method on a remote server is as simple as:

    $Client = new JsonRpc\Client($url);
    $Client->call('method', array($param1, $param2));

    // now do something with $Client->result

And at the server end:

    // MethodsClass contains the exposed methods
    $methods = new MethodsClass();

    $Server = new JsonRpc\Server($methods);
    $Server->receive();




  [json-spec]: http://www.jsonrpc.org/
