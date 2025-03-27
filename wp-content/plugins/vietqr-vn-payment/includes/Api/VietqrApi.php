<?php

namespace VietQR\Api;

use VietQR\Base\ApiClient;
use VietQR\Support\VietqrHelper;
use VietQR\Service\VietqrService;

class VietqrApi extends ApiClient {
    protected string $protocol;
    protected string $domain; 
    protected string $port;

    // Private constructor to prevent direct instantiation
    public function __construct() {
        parent::__construct(); 
        $this->domain = $_ENV['VIETQR_DOMAIN'];
        $this->protocol = "https";
        $this->port = "443";
    }

    /**
     * Get token
     *
     * @return array
     */
    public function get_token() {
        $path = "/vqr/api/token_generate";
        $url = $this->make_url($path);
        $username = $_ENV['VIETQR_USERNAME'];
        $password = $_ENV['VIETQR_PASSWORD'];

        // Prepare the request headers
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/json'
        ];

        // Prepare the POST data
        $data = [];

        // Make the POST request
        $response = $this->post($url, [
            'headers' => $headers,
            'json' => $data
        ]);

        return $response;
    }

    /**
     * Generate transaction
     *
     * @param array $order_info
     * @return array
     */
    public function generate_transaction(array $order_info) {
        $path = "/vqr/api/qr/generate-customer";
        $url = $this->make_url($path);
        $token = vr_get_authorization_code();
        $service = new VietqrService();
        $selected_bank_account = $service->get_selected_bank_account();
        $vietqr_account_number = $selected_bank_account["account_number"];
        $vietqr_account_name = $selected_bank_account["account_name"];
        $vietqr_bank_code = $selected_bank_account["bank_code"];
        $vqr_code = VietqrHelper::generate_vqr_code();
        $content = $vqr_code . " " . $order_info["order_id"];

        $data = [
            'bankAccount' => $vietqr_account_number,
            'amount' => $order_info["amount"],
            'transType' => 'C', // Default transaction type
            'content' => $content,
            'bankCode' => $vietqr_bank_code,
            'userBankName' => $vietqr_account_name,
            'orderId' => substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10)
        ];

        // Prepare the request headers
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        // Make the POST request
        $response = $this->post($url, [
            'headers' => $headers,
            'json' => $data
        ]);

        return $response;
    }

    /**
     * Get bank accounts
     *
     * @return array
     */
    public function get_bank_accounts() {
        $path = "/vqr/api/account-bank/wp";
        $url = $this->make_url($path);
        $token = vr_get_authorization_code();

        $response = $this->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);

        return $response;
    }

    /**
     * Get bank image
     *
     * @param string $img_id
     * @return mixed
     */
    public function get_bank_image(string $img_id) {
        $path = "/vqr/api/images/$img_id";
        $url = $this->make_url($path);
        $token = vr_get_authorization_code();

        $response = $this->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);

        return $response;
    }

}