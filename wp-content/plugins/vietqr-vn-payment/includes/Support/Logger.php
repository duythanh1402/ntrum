<?php
/**
 * Logger class for logging messages using Monolog.
 * 
 * @package VietQR
 */

namespace VietQR\Support;

use VietQR\Monolog\Logger as MonologLogger;
use VietQR\Monolog\Handler\StreamHandler;

class Logger {
    private $logger;

    public function __construct($name = 'VietQR', $log_file = 'path/to/logfile.log', $log_level = MonologLogger::DEBUG) {
        $this->logger = new MonologLogger($name);
        $this->logger->pushHandler(new StreamHandler($log_file, $log_level));
    }

    /**
     * Log a debug message.
     */
    public function debug($message, array $context = []) {
        $this->logger->debug($message, $context);
    }

    /**
     * Log an info message.
     */
    public function info($message, array $context = []) {
        $this->logger->info($message, $context);
    }

    /**
     * Log a warning message.
     */
    public function warning($message, array $context = []) {
        $this->logger->warning($message, $context);
    }

    /**
     * Log an error message.
     */
    public function error($message, array $context = []) {
        $this->logger->error($message, $context);
    }
}