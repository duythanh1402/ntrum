<?php
/**
 * Database functions
 *
 * @package VietQR
 */

// Create table vr_transactions when plugin activated
function vr_create_transactions_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vr_transactions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        woo_order_id BIGINT(20) NOT NULL UNIQUE COMMENT 'ID của đơn hàng trong WooCommerce',
        `status` smallint(6) NOT NULL DEFAULT 0 COMMENT 'Trạng thái giao dịch',
        bank_code VARCHAR(255) COMMENT 'Mã ngân hàng',
        bank_name VARCHAR(255) COMMENT 'Tên ngân hàng',
        bank_account VARCHAR(255) COMMENT 'Số tài khoản',
        user_bank_name VARCHAR(255) COMMENT 'Tên người gửi',
        amount VARCHAR(255) COMMENT 'Số tiền',
        content TEXT,
        qr_code TEXT,
        img_id VARCHAR(255) COMMENT 'ID của hình ảnh QR code',
        existing INT,
        transaction_id VARCHAR(255),
        transaction_time VARCHAR(255) COMMENT 'Thời gian giao dịch',
        transaction_ref_id VARCHAR(255),
        qr_link VARCHAR(255),
        terminal_code VARCHAR(255),
        sub_terminal_code VARCHAR(255),
        service_code VARCHAR(255),
        order_id VARCHAR(255),
        additional_data TEXT,
        PRIMARY KEY (woo_order_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
 
// Delete table vr_transactions when plugin uninstalled
function vr_delete_transactions_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vr_transactions';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

// Create table vr_bank_accounts when plugin activated
function vr_create_bank_accounts_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vr_bank_accounts';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        bank_code VARCHAR(255) NOT NULL COMMENT 'Mã ngân hàng',
        bank_name VARCHAR(255) NOT NULL COMMENT 'Tên ngân hàng',
        account_number VARCHAR(255) NOT NULL COMMENT 'Số tài khoản',
        account_name VARCHAR(255) NOT NULL COMMENT 'Tên chủ tài khoản',
        branch VARCHAR(255) COMMENT 'Chi nhánh ngân hàng',
        is_selected TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Tài khoản đang sử dụng',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Delete table vr_bank_accounts when plugin uninstalled
function vr_delete_bank_accounts_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vr_bank_accounts';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
