<?php


namespace Oesteve\Tests\Transformer\Logger;

use Psr\Log\LoggerInterface;

class TraceableLogger implements LoggerInterface
{
    public array $entries = [];

    private function setEntry(string $level, string $message, array $context): void
    {
        if (!isset($this->entries[$level])) {
            $this->entries[$level] = [];
        }

        $this->entries[$level][] = [$message, $context];
    }

    public function emergency($message, array $context = array())
    {
        $this->setEntry('emergency', $message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->setEntry('alert', $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->setEntry('critical', $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->setEntry('error', $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->setEntry('warning', $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->setEntry('notice', $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->setEntry('info', $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->setEntry('debug', $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->setEntry('log', $message, $context);
    }

    public function clear():void
    {
        $this->entries = [];
    }
}
