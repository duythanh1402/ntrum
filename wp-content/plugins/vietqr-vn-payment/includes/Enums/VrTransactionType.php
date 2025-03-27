<?php
/**
 * VrTransactionType
 *
 * @package VietQR\Enums
 */

namespace VietQR\Enums;

class VrTransactionType {
    const QR_TRANSACTION = 0;
    const QR_STORE = 1;
    const OTHER_TRANSACTION = 2;

    public static function get_label($type) {
        switch ($type) {
            case self::QR_TRANSACTION:
                return 'QR giao dịch';
            case self::QR_STORE:
                return 'QR cửa hàng';
            case self::OTHER_TRANSACTION:
                return 'Giao dịch khác';
            default:
                return 'Unknown';
        }
    }
}
