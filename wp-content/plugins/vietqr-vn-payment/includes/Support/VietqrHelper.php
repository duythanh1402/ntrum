<?php
/**
 * VietQR Helper
 *
 * @package VietQR
 */

namespace VietQR\Support;

class VietqrHelper {
    /**
     * Generate VQR code
     *
     * @return string
     */
    public static function generate_vqr_code() : string {
        $prefix = 'VQR';
        $random_number = mt_rand(0, 9999999999);
        $padded_number = str_pad($random_number, 10, '0', STR_PAD_LEFT);
        $vqr_code = $prefix . $padded_number;

        return $vqr_code;
    }
}
