<?php
/**
 * WooOrderStatus
 *
 * @package VietQR\Enums
 */

namespace VietQR\Enums;

class WooOrderStatus {
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const ON_HOLD = 'on-hold';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';
    const REFUNDED = 'refunded';
    const FAILED = 'failed';
}