<?php

namespace VietQR\Api;

use VietQR\Base\ApiClient;

class VietqrEcomerceApi extends ApiClient {
    protected string $protocol;
    protected string $domain; 
    protected string $port;
    protected string $access_key;

    public function __construct() {
        parent::__construct(); 
        $this->domain = $_ENV['VIETQR_DOMAIN'];
        $this->protocol = "https";
        $this->port = "443";
        $this->access_key = $_ENV['VIETQR_ECOMMERCE_ACCESS_KEY'];
    }

    /**
     * Get token
     *
     * @return array
     */
    public function get_token() {
        $path = "/vqr/api/peripheral/ecommerce/token_generate";
        $url = $this->make_url($path);
        $username = $_ENV['VIETQR_ECOMMERCE_USERNAME'];
        $password = $_ENV['VIETQR_ECOMMERCE_PASSWORD'];

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
     * Get active ecommerce information
     * 
     * @return array
     */
    public function get_active_ecommerce() {
        $path = "/vqr/api/ecommerce";
        $url = $this->make_url($path);
        $token = vr_get_ecommerce_token();
        $password = $_ENV['VIETQR_ECOMMERCE_PASSWORD'];
        $ecommerce_site = $this->get_ecommerce_site();
        $checksum = md5($password . ":" . $ecommerce_site . $this->access_key); // checksum = md5(password + ":" + ecommerce_site + ":" + access_key)

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        $body = [
            'ecommerceSite' => $ecommerce_site,
            'checkSum' => $checksum
        ];

        $response = $this->post($url, [
            'headers' => $headers,
            'json' => $body
        ]);
        
        return $response;
    }

    /**
     * Get ecommerce site
     * 
     * @return string
     */
    public function get_ecommerce_site() {
        return home_url();
    }

    /**
     * Get order information
     * 
     * @param string $vr_order_id VietQR transaction order id
     * @param string $account_number Account number
     * @param string $bank_code Bank code
     * @return array containing referenceNumber, orderId, amount, content, transType, status, type, timeCreated, timePaid, terminalCode, note, refundCount, amountRefund
     */
    public function get_order_info( $vr_order_id, $account_number, $bank_code ) {
        $path = "/vqr/api/ecommerce-transactions/check-order";
        $url = $this->make_url($path);
        $token = vr_get_ecommerce_token();
        $username = $_ENV['VIETQR_ECOMMERCE_USERNAME'];
        $checksum = md5($account_number . $username); // checksum

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        $body = [
            'bankAccount' => $account_number, // acount number
            'bankCode' => $bank_code, // bank code
            'type' => 0, // Default
            'value' => $vr_order_id, // VietQR transaction order id
            'checkSum' => $checksum 
        ];

        error_log(print_r($body, true));

        $response = $this->post($url, [
            'headers' => $headers,
            'json' => $body
        ]);
        
        return $response;
    }
}