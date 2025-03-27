<?php
/**
 * Ajax functions
 * 
 * @package VietQR
 */

use VietQR\Api\VietqrEcomerceApi;
use VietQR\Query\VrTransaction;
use VietQR\Enums\WooOrderStatus;
use VietQR\Enums\VrTransactionStatus;
use VietQR\Service\VietqrService;
use VietQR\Query\VrBankAccount;

/**
 * Ajax function to store vietqr_selected_bank_account option
 */
function vr_store_bank_account() {
    if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'vr_store_bank_account' ) ) {
        wp_send_json_error('Không có quyền truy cập');
        exit;
    }

    if (isset($_POST['bank_account'])) {
        $notification_type = $_POST['notification_type'];

        // Check notification type
        if ($notification_type != 'N22') {
            wp_send_json_error("Loại thông báo không chính xác");
            exit;
        }

        // Store bank account
        $service = new VietqrService();
        $result = $service->add_bank_account($_POST['bank_account']);

        if ($result == 1) {
            wp_send_json_success('Cập nhật thành công');
        } else if ($result == 2) {
            wp_send_json_error('Tài khoản ngân hàng đã tồn tại');
        } else {
            wp_send_json_error('Cập nhật thất bại');
        }
    } else {
        wp_send_json_error('Không có dữ liệu');
    }
    exit;
}
add_action('wp_ajax_vr_store_bank_account', 'vr_store_bank_account');

/**
 * Ajax function to store vietqr_bank_transfer_enabled option
 */
function vr_store_bank_transfer_enabled() {
    if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'vr_store_bank_transfer_enabled' ) ) {
        wp_send_json_error('Không có quyền truy cập');
        exit;
    }
    
    if (isset($_POST['bank_transfer_enabled'])) {
        // sanitize bool value
        $bank_transfer_enabled = filter_var($_POST['bank_transfer_enabled'], FILTER_VALIDATE_BOOLEAN);
        vr_set_bank_transfer_enabled($bank_transfer_enabled);
        wp_send_json_success('Cập nhật thành công');
    } else {
        wp_send_json_error('Không có dữ liệu');
    }
    exit;
}
add_action('wp_ajax_vr_store_bank_transfer_enabled', 'vr_store_bank_transfer_enabled');

/**
 * Ajax function to update order as completed
 */
function vr_update_order_completed() {
    if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'vr_update_order_completed' ) ) {
        wp_send_json_error('Không có quyền truy cập');
        exit;
    }

    if (isset($_POST['data'])) {
        $data = $_POST['data'];

        // Check notification type
        if ($data['notificationType'] != 'N05') {
            wp_send_json_error("Loại thông báo không đúng");
            exit;
        }

        $transaction = VrTransaction::get_by_order_id($data['orderId']);

        // Check transaction exist
        if (!$transaction) {
            wp_send_json_error("Đơn hàng không tồn tại");
            exit;
        }

        // Check orderId
        if ($data['orderId'] != $transaction["orderId"]) {
            wp_send_json_error("Mã đơn hàng không đúng");
            exit;
        }

        // Check amount
        if ($data['amount'] != $transaction["amount"]) {
            wp_send_json_error("Số tiền không đúng");
            exit;
        }

        // Update transaction data
        VrTransaction::update(
            $transaction['wooOrderId'],
            [
                'status' => VrTransactionStatus::SUCCESS,
                'transactionTime' => $data['transactionTime'],
            ]
        );

        // Update woo order status
        $order = wc_get_order($transaction['wooOrderId']);
        $order->update_status(WooOrderStatus::COMPLETED);

        wp_send_json_success('Đơn hàng đã thanh toán');
    } else {
        wp_send_json_error('Không có dữ liệu');
    }
    exit;
}
add_action('wp_ajax_nopriv_vr_update_order_completed', 'vr_update_order_completed');
add_action('wp_ajax_vr_update_order_completed', 'vr_update_order_completed');

/**
 * Ajax function to manual check transaction
 */
function vr_manual_check() {
    if ( !wp_verify_nonce( $_POST['_wpnonce'], 'vr_manual_check' ) ) {
        wp_send_json_error('Không có quyền truy cập');
        exit;
    }

    if (isset($_POST['orderId'])) {
        $vr_order_id = $_POST['orderId'];
        $transaction = VrTransaction::get_by_order_id($vr_order_id);

        // Check transaction exist
        if (!$transaction) {
            wp_send_json_error('Đơn hàng không tồn tại');
            exit;
        }

        // Check transaction status from API
        $api = new VietqrEcomerceApi();
        $vr_transaction = $api->get_order_info(
            $transaction['orderId'], $transaction['bankAccount'], $transaction['bankCode']
        );

        // Check transaction status
        if ($vr_transaction[0]['status'] == VrTransactionStatus::SUCCESS) {
            // Update transaction data
            VrTransaction::update(
                $transaction['wooOrderId'],
                [
                    'status' => VrTransactionStatus::SUCCESS,
                    'transactionTime' => $vr_transaction[0]['timePaid'],
                ]
            );

            // Update woo order status
            $order = wc_get_order($transaction['wooOrderId']);
            $order->update_status(WooOrderStatus::COMPLETED);

            wp_send_json_success('Đơn hàng đã thanh toán');
        } else {
            wp_send_json_error('Đơn hàng chưa thanh toán');
        }
    } else {
        wp_send_json_error('Mã đơn hàng không đúng');
    }

    exit;
}
add_action('wp_ajax_nopriv_vr_manual_check', 'vr_manual_check');
add_action('wp_ajax_vr_manual_check', 'vr_manual_check');

/**
 * AJAX handler for selecting a bank account
 */
function vr_select_bank_account() {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'vr_select_bank_account')) {
        wp_send_json_error('Invalid nonce');
        exit;
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        exit;
    }

    if (!isset($_POST['id']) || empty($_POST['id'])) {
        wp_send_json_error('Bank account ID is required');
        exit;
    }

    $bank_account_id = intval($_POST['id']);

    $query = new VrBankAccount();

    // Check if bank account is exist
    $bank_account = $query->find_where(['id' => $bank_account_id]);
    if (!$bank_account) {
        wp_send_json_error('Bank account not found');
        exit;
    }

    // Set the chosen bank account as selected
    $query->update_where(['is_selected' => 0]); // Deselect all bank accounts
    $result = $query->update_where(['is_selected' => 1], ['id' => $bank_account_id]); // Select the chosen bank account
    if (!empty($result)) {
        wp_send_json_success('Bank account selected successfully');
    } else {
        wp_send_json_error('Failed to select bank account');
    }

    exit;
}
add_action('wp_ajax_vr_select_bank_account', 'vr_select_bank_account');
