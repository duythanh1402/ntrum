<?php
/**
 * VietQR Transaction Query
 *
 * @package VietQR
 */

namespace VietQR\Query;

use VietQR\Base\BaseQuery;

class VrTransaction extends BaseQuery
{
    protected static $table_name = 'vr_transactions';
    protected static $primary_key = 'woo_order_id';

    protected static $field_mapping = [
        'wooOrderId' => 'woo_order_id',
        'bankCode' => 'bank_code',
        'bankName' => 'bank_name',
        'bankAccount' => 'bank_account',
        'userBankName' => 'user_bank_name',
        'amount' => 'amount',
        'content' => 'content',
        'qrCode' => 'qr_code', // This is string for QR code
        'imgId' => 'img_id',
        'existing' => 'existing',
        'transactionId' => 'transaction_id',
        'transactionTime' => 'transaction_time',
        'transactionRefId' => 'transaction_ref_id',
        'qrLink' => 'qr_link',
        'terminalCode' => 'terminal_code',
        'subTerminalCode' => 'sub_terminal_code',
        'serviceCode' => 'service_code',
        'orderId' => 'order_id',
        'additionalData' => 'additional_data', // This is an serialized data
    ];

    /**
     * Get a record by order id.
     * 
     * @param int $order_id VietQR transaction order id
     * @return array|null
     */
    public static function get_by_order_id($order_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE order_id = %s", $order_id);
        $result = $wpdb->get_row($query, ARRAY_A);

        if ($result) {
            return static::unapply_field_mapping($result);
        }

        return null;
    }
}