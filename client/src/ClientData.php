<?php

namespace Iggyvolz\Sabifier;

final class ClientData implements \JsonSerializable
{
    private function __construct(
        private string $mac,
        private string $ip,
        private ?string $currentImage
    ) {}

    public static function get(): self
    {
        return new self(
            self::macAddress(),
            self::ipAddress(),
            self::currentImage(),
        );
    }

    private static function macAddress(): string
    {
        return ArpResponse::get()->hwaddress;
    }

    private static function ipAddress(): string
    {
        return ArpResponse::get()->ip;
    }

    private static function currentImage(): ?string
    {
        return file_exists(__DIR__ . "/../image.txt") ? trim(file_get_contents(__DIR__ . "/../image.txt")) : null;
    }

    public function jsonSerialize(): array
    {
        return [
            "mac" => $this->mac,
            "ip" => $this->ip,
            "currentImage" => $this->currentImage,
        ];
    }
}