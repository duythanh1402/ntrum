<?php
/**
 * Options functions
 *
 * @package VietQR
 */

use VietQR\Service\VietqrService;

/**
 * Register the VietQR Config options
 */
function vr_register_settings() {
    //  Normal Api
    register_setting('vietqr_options', 'vietqr_bank_transfer_enabled');
    register_setting('vietqr_options', 'vietqr_qr_code_enabled');
    register_setting('vietqr_options', 'vietqr_authorization_code');
    register_setting('vietqr_options', 'vietqr_transaction_prefix');

    //  Ecommerce Api
    register_setting('vietqr_ecommerce_options', 'vietqr_ecommerce_token');
    register_setting('vietqr_ecommerce_options', 'vietqr_ecommerce_webhook');
    register_setting('vietqr_ecommerce_options', 'vietqr_ecommerce_clientid');
    register_setting('vietqr_ecommerce_options', 'vietqr_ecommerce_certificate');
}
add_action('admin_init', 'vr_register_settings');

/**
 * Get VietQR bank transfer enabled option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_bank_transfer_enabled() {
    return get_option('vietqr_bank_transfer_enabled');
}

/**
 * Set VietQR bank transfer enabled option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_bank_transfer_enabled($option_value) {
    return update_option('vietqr_bank_transfer_enabled', $option_value);
}

/**
 * Get VietQR QR code enabled option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_qr_code_enabled() {
    return get_option('vietqr_qr_code_enabled');
}

/**
 * Set VietQR QR code enabled option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_qr_code_enabled($option_value) {
    return update_option('vietqr_qr_code_enabled', $option_value);
}

/**
 * Get VietQR authorization code option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_authorization_code() {
    return get_option('vietqr_authorization_code');
}

/**
 * Set VietQR authorization code option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_authorization_code($option_value) {
    return update_option('vietqr_authorization_code', $option_value);
}

/**
 * Get VietQR selected bank account option
 * 
 * @return array array of account_number, account_name, bank_code
 */
function vr_get_selected_bank_account() {
    $service = new VietqrService();
    return $service->get_selected_bank_account();
}

/**
 * Set VietQR selected bank account option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_selected_bank_account($option_value) {
    return update_option('vietqr_selected_bank_account', $option_value);
}

/**
 * Get VietQR transaction prefix option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_transaction_prefix() {
    return get_option('vietqr_transaction_prefix');
}

/**
 * Set VietQR transaction prefix option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_transaction_prefix($option_value) {
    return update_option('vietqr_transaction_prefix', $option_value);
}

/**
 * Get VietQR Ecommerce Token option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_ecommerce_token() {
    return get_option('vietqr_ecommerce_token');
}

/**
 * Set VietQR Ecommerce Token option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_ecommerce_token($option_value) {
    return update_option('vietqr_ecommerce_token', $option_value);
}

/**
 * Get VietQR Ecommerce Webhook option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_ecommerce_webhook() {
    return get_option('vietqr_ecommerce_webhook');
}

/**
 * Set VietQR Ecommerce Webhook option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_ecommerce_webhook($option_value) {
    return update_option('vietqr_ecommerce_webhook', $option_value);
}

/**
 * Get VietQR Ecommerce Client ID option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_ecommerce_clientid() {
    return get_option('vietqr_ecommerce_clientid');
}

/**
 * Set VietQR Ecommerce Client ID option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_ecommerce_clientid($option_value) {
    return update_option('vietqr_ecommerce_clientid', $option_value);
}

/**
 * Get VietQR Ecommerce Certificate option
 * 
 * @return mixed The value of the option if it exists, otherwise false.
 */
function vr_get_ecommerce_certificate() {
    return get_option('vietqr_ecommerce_certificate');
}

/**
 * Set VietQR Ecommerce Certificate option
 * 
 * @param mixed $option_value The value to set for the option.
 * @return bool True if the option was updated, false otherwise.
 */
function vr_set_ecommerce_certificate($option_value) {
    return update_option('vietqr_ecommerce_certificate', $option_value);
}