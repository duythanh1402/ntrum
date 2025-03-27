<?php
/**
 * WooCommerce functions
 * 
 * @package VietQR
 */

use VietQR\Query\VrTransaction;
use VietQR\Service\VietqrService;
use VietQR\Enums\VrTransactionStatus;

/**
 * Custom woocommerce payment method title
 *
 * @param $title
 * @param $gateway_id
 *
 * @return string
 */
function vr_payment_method_title($title, $gateway_id) {
    // Define an array of payment methods and their corresponding images
    $payment_methods = array(
        'bacs' => "Chuyển khoản ngân hàng VietQR VN",
    );

    // Get text of correspond methods
    if (isset($payment_methods[$gateway_id])) {
        $title = $payment_methods[$gateway_id];
    }

    return $title;
}
add_filter('woocommerce_gateway_title', 'vr_payment_method_title', 10, 2);

/**
 * Custom woocommerce payment method description
 *
 * @param $title
 * @param $gateway_id
 *
 * @return string
 */
function vr_payment_method_description ($desc, $gateway_id) {
    // Define an array of payment methods and their corresponding images
    $payment_methods = array(
        'bacs' => "Thanh toán bằng chuyển khoản Ngân hàng qua phương pháp Quét mã VietQR, được cung cấp bởi Dịch vụ 
        <a style='color: blue; font-size: .92em;' href='https://vietqr.vn/'>VietQR.vn</a>",
    );

    // Get text of correspond methods
    if (isset($payment_methods[$gateway_id])) {
        $desc = $payment_methods[$gateway_id];
    }

    return $desc;
}
// add_filter('woocommerce_gateway_description', 'vr_payment_method_description', 10, 2);

/**
 * Custom woocommerce payment method icon
 *
 * @param $icon_html
 * @param $gateway_id
 *
 * @return mixed|string
 */
function vr_payment_method_icon($icon_html, $gateway_id) {
    $payment_methods = array(
        'bacs' => VIETQR_URL . "/public/img/vietqr_payment_1x.png",
    );

    // Check if the payment method has a corresponding image
    if (isset($payment_methods[$gateway_id])) {
        $image_url = $payment_methods[$gateway_id];
        $icon_html = '<img width="70px" src="' . esc_url($image_url) . '" alt="' . esc_attr($gateway_id) . '">';
    }

    return $icon_html;
}
add_filter('woocommerce_gateway_icon', 'vr_payment_method_icon', 10, 2);

/**
 * Woo thank you page custom
 * @param $order_id
 *
 * @return void
 * @throws Exception
 */
function vr_woo_custom_thankyou($order_id) {
    $vr_transaction = VrTransaction::get_by_id($order_id);

    // Get selected bank account
    $service = new VietqrService();
    $bank_account = $service->get_selected_bank_account();

    // Get order id and order code
    $order = wc_get_order($order_id);
    $order_id = $order->get_id();
    $order_code = $order_id . "." . vr_get_domain();
    $payment_method = $order->get_payment_method();
    
    // Make websocket url
    $client_id = vr_get_ecommerce_clientid();
    $ws_url = "wss://api.vietqr.org/vqr/socket?clientId={$client_id}";
    
    // Generate transaction if not exist
    $service = new VietqrService(); // Init service
    if (empty($vr_transaction)) {
        $transaction = $service->generate_transaction([
            "amount" => vr_convert_currency_to_number($order->get_total()),
            "order_id" => $order->get_id()
        ]);
        $transaction['wooOrderId'] = $order_id;

        // Insert transaction to database
        VrTransaction::insert($transaction);
    } else {
        $transaction = $vr_transaction;
    }

    $is_completed = ($vr_transaction['status'] == VrTransactionStatus::SUCCESS);
    $qr_code = $service->generate_qr_code($transaction['qrCode']); // Generate QR code base64 

    vr_get_public_template('woo-thank-you', 
        compact(
            'order', 'transaction', 'qr_code', 'ws_url', 'is_completed', 
            'payment_method', 'bank_account', 'order_id', 'order_code'
            )
    );
}
add_action('woocommerce_thankyou', 'vr_woo_custom_thankyou');