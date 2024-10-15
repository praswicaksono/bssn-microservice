<?php

require_once './vendor/autoload.php';

$http = new Swoole\Http\Server('0.0.0.0', 9501);
$http->set(['hook_flags' => SWOOLE_HOOK_ALL]);

$http->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    $auth = $request->header['authorization'] ?? null;
    var_dump($auth);
    var_dump($request->header);
    if ($auth === '123') {
        echo "Request Accept";
        $response->setHeader('X-User-ID', 1);
        $response->setStatusCode(200);
    } else {
        echo "Request Rejected";
        $response->setHeader('Location', 'https://google.com');
        $response->setStatusCode(403);
    }
});

$http->start();
