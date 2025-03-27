<?php
/**
 * VietQR Service
 * 
 * @package VietQR
 * @subpackage Service
 * @since 1.0.0
 */

namespace VietQR\Service;

use VietQR\Api\VietqrEcomerceApi;
use VietQR\Api\VietqrApi;
use VietQR\chillerlan\QRCode\QRCode;
use VietQR\chillerlan\QRCode\QROptions;
use VietQR\Query\VrBankAccount;

class VietqrService
{
    /**
     * @var VietqrEcomerceApi
     */
    private $ecommerceApi;

    /**
     * @var VietqrApi
     */
    private $api;

    public function __construct()
    {
        $this->ecommerceApi = new VietqrEcomerceApi();
        $this->api = new VietqrApi();
    }

    /**
     * Generate VietQR transaction
     * 
     * @param array $order_info
     * @return array
     */
    public function generate_transaction(array $order_info)
    {
        // Get token via api
        $token_result = $this->api->get_token();
        $token = $token_result['access_token'];

        // Store the token for later use
        vr_set_authorization_code($token);

        // Generate transaction
        $transaction = $this->api->generate_transaction($order_info);

        return $transaction;
    }

    /**
     * Get active websocket url
     * 
     * @return string
     */
    public function get_ecommerce_info()
    {
        $token_result = $this->ecommerceApi->get_token();
        $token = $token_result['access_token'];
        
        // Store the token for later use
        vr_set_ecommerce_token($token);

        $active_result = $this->ecommerceApi->get_active_ecommerce();
        $clientId = $active_result['clientId'];
        $certificate = $active_result['certificate'];

        // Store for later use
        vr_set_ecommerce_clientid($clientId);
        vr_set_ecommerce_certificate($certificate);

        echo $certificate; // test code

        $ws_url = "wss://api.vietqr.org/vqr/socket?clientId={$clientId}";
        $qr_code = $this->generate_qr_code($certificate);

        return compact('ws_url', 'qr_code', 'clientId');
    }

    /**
     * Generate QR code image from a string
     *
     * @param string $data The string to encode in the QR code
     * @param int $size The size of the QR code image (default: 300)
     * @return string Base64 encoded image data
     */
    public function generate_qr_code(string $data, int $size = 300): string {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => true,
            'imageTransparent' => false,
        ]);

        $qrcode = new QRCode($options);
        $image = $qrcode->render($data);

        return $image;
    }

    /**
     * Get bank accounts list
     * 
     * @return array
     */
    public function get_bank_accounts()
    {
        $vr_bank_account = new VrBankAccount();
        return $vr_bank_account->get_all();
    }

    /**
     * Get selected bank account
     * 
     * @return array|null An associative array containing the selected bank account details:
     *               - 'id' (int) The unique identifier of the bank account
     *               - 'account_number' (string) The bank account number
     *               - 'account_name' (string) The name associated with the bank account
     *               - 'bank_code' (string) The code of the bank
     *               - 'is_selected' (int) Always 1 for the selected account
     *               ...
     *               Returns null if no bank account is selected.
     */
    public function get_selected_bank_account()
    {
        $vr_bank_account = new VrBankAccount();
        $result = $vr_bank_account->find_where(['is_selected' => 1]);
        return $result[0] ?? null;
    }

    /**
     * Add new bank account
     * 
     * @param array $data
     * @return int 1 if success, 0 if failed, 2 if already exist
     */
    public function add_bank_account(array $data)
    {
        // Double check if bank account is exist
        $vr_bank_account = new VrBankAccount();
        try {
            $bank_account = $vr_bank_account->find_where(['account_number' => $data['account_number']]);

            if (empty($bank_account)) {
                $vr_bank_account->insert_many([$data]);
                return 1;
            }

            return 2;
        } catch (\Exception $e) {
            // Log the error if needed failed to query database
            // error_log($e->getMessage());
            return 0;
        }
    }
}           