<?php
/**
 * Admin Management
 * 
 * @package VietQR
 * @since 1.0.0
 */

/**
 * Register the VietQR Config menu
 */
function vr_register_menu() {
    add_menu_page(
        'VietQR VN',
        'VietQR VN',
        'manage_options',
        'vietqr-admin',
        'vr_admin_page_config',
        'dashicons-admin-generic',
        99
    );
}
add_action('admin_menu', 'vr_register_menu');

/**
 * Render the VietQR Config page
 */
function vr_admin_page_config() {
    // Prepare data
    $service = new \VietQR\Service\VietqrService();
    $ecommerce_info = $service->get_ecommerce_info();
    $ws_url = $ecommerce_info['ws_url'];
    $qr_code = $ecommerce_info['qr_code'];
    $bank_accounts = $service->get_bank_accounts();
    $selected_bank_account = $service->get_selected_bank_account();

    // Render
    vr_get_admin_template('vietqr-config', 
        compact('ws_url', 'qr_code', 'selected_bank_account', 'bank_accounts')
    );
}