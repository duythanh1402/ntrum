<?php
/**
 * Flash message support.
 */

namespace VietQR\Support;

class Flash {
    const ERROR_TRANSIENT_PREFIX = 'fb_flash_error_';
    const SUCCESS_TRANSIENT_PREFIX = 'fb_flash_success_';
    const OLD_TRANSIENT_PREFIX = 'fb_flash_old_';

    /**
     * Set a flash message and store previous messages.
     *
     * @param mixed $message The message content.
     * @param string $type The type of message (e.g., 'error', 'success').
     * @param int $expiration The expiration time in seconds.
     */
    public static function set($message, $type = 'old', $expiration = 60) {
        $transient_key = self::get_transient_key($type);
        set_transient($transient_key, maybe_serialize($message), $expiration);
    }

    /**
     * Get and clear all error messages.
     *
     * @return array|null The error messages data or null if not found.
     */
    public static function get_errors() {
        return self::get_and_clear('error');
    }

    /**
     * Get and clear all success messages.
     *
     * @param string $key The key for the messages.
     * @return array|null The success messages data or null if not found.
     */
    public static function get_successes() {
        return self::get_and_clear('success');
    }

    /**
     * Set an error message.
     *
     * @param array $message The message content.
     * @param int $expiration The expiration time in seconds.
     * @throws \InvalidArgumentException Message not an array.
     */
    public static function set_error($message, $expiration = 60) {
        if (!is_array($message)) {
            throw new \InvalidArgumentException('Message must be an array');
        }
        self::set($message, 'error', $expiration);
    }

    /**
     * Set a success message.
     *
     * @param array $message The message content.
     * @param int $expiration The expiration time in seconds.
     * @throws \InvalidArgumentException Message not an array.
     */
    public static function set_success($message, $expiration = 60) {
        if (!is_array($message)) {
            throw new \InvalidArgumentException('Message must be an array');
        }
        self::set($message, 'success', $expiration);
    }

    /**
     * Get and clear messages of a specific type.
     *
     * @param string $type The type of messages (e.g., 'error', 'success').
     * @return array|null The messages data or null if not found.
     */
    private static function get_and_clear($type) {
        $transient_key = self::get_transient_key($type);
        $data = get_transient($transient_key);
        if ($data) {
            delete_transient($transient_key);
        }
        return maybe_unserialize($data);
    }

    /**
     * Get the transient key based on the message type.
     *
     * @param string $key The key for the message.
     * @param string $type The type of message (e.g., 'error', 'success', 'old' ...).
     * @return string The transient key.
     */
    private static function get_transient_key($type) {
        switch ($type) {
            case 'error':
                return self::ERROR_TRANSIENT_PREFIX;
            case 'success':
                return self::SUCCESS_TRANSIENT_PREFIX;
            case 'old':
                return self::OLD_TRANSIENT_PREFIX;
            default:
                throw new \InvalidArgumentException("Invalid message type: $type");
        }
    }
}