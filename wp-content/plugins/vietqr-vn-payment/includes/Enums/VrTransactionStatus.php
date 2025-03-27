<?php
/**
 * VrTransactionStatus
 *
 * @package VietQR\Enums
 */

namespace VietQR\Enums;

class VrTransactionStatus {
    const PENDING = 0;
    const SUCCESS = 1;
    const CANCELLED = 2;

    public static function get_label($status) {
        switch ($status) {
            case self::PENDING:
                return 'Chờ thanh toán';
            case self::SUCCESS:
                return 'Thành công';
            case self::CANCELLED:
                return 'Đã hủy';
            default:
                return 'Unknown';
        }
    }
}
