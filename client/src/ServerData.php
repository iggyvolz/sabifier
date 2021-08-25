<?php

namespace Iggyvolz\Sabifier;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;

final class ServerData
{
    private function __construct(
        public /* readonly */ ?string $image,
        public /* readonly */ ?string $cmd,
    ) {}

    public static function get(string $url, string $key): self
    {
        $guzzle = new Client();
        $response = $guzzle->post($url, [
            "json" => ClientData::get()
        ]);
        $body = (array)JWT::decode($response->getBody()->getContents(), $key, ["EdDSA"]);
        $image = $body["image"] ?? null;
        $cmd = $body["cmd"] ?? null;
        if(!is_string($image) && !is_null($image)) {
            throw new \RuntimeException("Inappropriate response from server");
        }
        if(!is_string($cmd) && !is_null($cmd)) {
            throw new \RuntimeException("Inappropriate response from server");
        }
        return new self($image, $cmd);
    }
}