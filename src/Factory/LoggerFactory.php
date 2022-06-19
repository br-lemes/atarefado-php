<?php

namespace App\Factory;

use DateTimeZone;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    private $path;
    private $level;
    private $file_permission;
    private $handler = [];

    public function __construct(array $settings)
    {
        $this->path = (string)$settings['path'];
        $this->level = (int)$settings['level'];
        $this->file_permission = $settings['file_permission'];
    }

    public function createInstance(string $name): LoggerInterface
    {
        $logger = new Logger($name);
        $timezone = new DateTimeZone('-0400');
        $logger->setTimezone($timezone);
        foreach ($this->handler as $handler) {
            $logger->pushHandler($handler);
        }
        $this->handler = [];
        return $logger;
    }

    public function addFileHandler(string $filename, int $level = null): self
    {
        $level = $level ?? $this->level;
        $filename = "{$this->path}/{$filename}";
        $handler = new RotatingFileHandler($filename, 0, $level, true, $this->file_permission);
        $handler->setFormatter(new LineFormatter(null, 'Y-m-d H:i:s', false, true));
        $this->handler[] = $handler;
        return $this;
    }

    public function addConsoleHandler(int $level = null): self
    {
        $streamHandler = new StreamHandler('php://stdout', $level ?? $this->level);
        $streamHandler->setFormatter(new LineFormatter(null, null, false, true));
        $this->handler[] = $streamHandler;
        return $this;
    }
}
