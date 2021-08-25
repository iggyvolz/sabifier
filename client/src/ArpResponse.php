<?php

namespace Iggyvolz\Sabifier;

final class ArpResponse
{
    private function __construct(
        public /* readonly */ string $domain,
        public /* readonly */ string $ip,
        public /* readonly */ string $hwaddress,
    ) {}
    private static ?ArpResponse $self = null;
    public static function get(): self
    {
        return self::$self ??= self::realGet();
    }

    private static function realGet(): self
    {
        $response = exec("arp -a -i eth0 2>&1", $_, $responseCode);
        if($responseCode !== 0) {
            throw new \RuntimeException("Error response from arp");
        }
        $response = explode(" ", $response);
        $response = array_filter($response, fn(string $entry) => trim($entry) !== "");
        [$domain, $parenthesizedip, $_, $hwaddress] = $response;
        $ip = str_replace(["(", ")"], ["", ""], $parenthesizedip);
        return new self($domain, $ip, $hwaddress);
    }
}