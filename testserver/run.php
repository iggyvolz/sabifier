<?php
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use Amp\Socket\Server;
use Firebase\JWT\JWT;
use Psr\Log\NullLogger;
require_once __DIR__ . "/vendor/autoload.php";
define("JWT_KEY", "hKGYrcMSCKQ6dj5/Pq93/r+uoXBclyP72rQp0G6Fv36S0OfI70qDD6thsoq4DhW3OK7z6a/csjhtrY3KytWAcw==");
// Pubkey: ktDnyO9Kgw+rYbKKuA4Vtziu8+mv3LI4ba2NysrVgHM=
Amp\Loop::run(function () {
    $sockets = [
        Server::listen("0.0.0.0:1337"),
        Server::listen("[::]:1337"),
    ];

    $server = new HttpServer($sockets, new CallableRequestHandler(function (Request $request) {
        $body = json_decode(yield $request->getBody()->buffer(), true);
        $mac = $body["mac"] ?? null;
        $ip = $body["ip"] ?? null;
        $currentImage = $body["mac"] ?? null;
        $newImage = match($mac) {
            "00:15:5d:57:1a:51" => "http://127.0.0.1:1337/image.iso",
            default => null
        };
        $cmd = match($mac) {
            "00:15:5d:57:1a:51" => "echo foo",
            default => null
        };
        $payload = [
            "image" => $newImage,
            "cmd" => $cmd,
            "iat" => time(),
            "exp" => time() + 60,
        ];
        return new Response(Status::OK, [
            "content-type" => "text/plain"
        ], JWT::encode(
            $payload,
            JWT_KEY,
            "EdDSA"
        ));
    }), new NullLogger);

    yield $server->start();
    if(extension_loaded("pcntl")) {
        // Stop the server gracefully when SIGINT is received.
        // This is technically optional, but it is best to call Server::stop().
        Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
            Amp\Loop::cancel($watcherId);
            yield $server->stop();
        });
    }
});