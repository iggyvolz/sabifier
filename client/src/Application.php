<?php

namespace Iggyvolz\Sabifier;

use GuzzleHttp\Client;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Application extends CLI
{
    public static function start(): void
    {
        (new self)->run();
    }

    protected function setup(Options $options): void
    {
        $options->setHelp("SABifier - a lightweight imaging software");
        $options->registerOption("dry-run", "run in dry run mode (don't actually image)", "d");
        $options->registerCommand("run", "Runs SABifier");
        $options->registerArgument("url", "remote URL to fetch from", true, "run");
        $options->registerArgument("key", "public key to use", true, "run");
    }

    protected function main(Options $options): void
    {
        if($options->getOpt("help") || $options->getOpt("h")) {
            $this->doHelp($options);
            return;
        }
        match($options->getCmd()) {
            "run" => $this->doRun($options, ...$options->getArgs()),
            default => $this->doHelp($options)
        };
    }

    private function doRun(Options $options, string $url, string $key): void
    {
        $serverData = ServerData::get($url, $key);
        if($options->getOpt("dry-run")) {
            if(!is_null($serverData->image)) {
                echo "curl " . escapeshellarg($serverData->image) . " --progress-bar|dd of=./dev_sda" . PHP_EOL;
            }
            if(!is_null($serverData->cmd)) {
                echo $serverData->cmd . PHP_EOL;
            }
        } else {
            if(!is_null($serverData->image)) {
                system("curl " . escapeshellarg($serverData->image) . " --progress-bar|dd of=./dev_sda");
            }
            if(!is_null($serverData->cmd)) {
                system($serverData->cmd);
            }
        }
    }

    private function doHelp(Options $options): void
    {
        echo $options->help();
    }
}