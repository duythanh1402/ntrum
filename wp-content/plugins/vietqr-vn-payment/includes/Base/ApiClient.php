<?php
/**
 * Base API Client class
 */

namespace VietQR\Base;

use VietQR\GuzzleHttp\Client;
use VietQR\GuzzleHttp\Exception\GuzzleException;
use VietQR\Support\Logger;

class ApiClient {
    protected $logger;
    protected string $logger_name = 'ApiClient';
    protected string $log_path = 'logs/api.log';
    protected string $protocol = "";
    protected string $domain = ""; 
    protected string $port = "";

    public function __construct() {
        // Log configuration
        $log_path = VIETQR_PATH . $this->log_path;
        $this->logger = new Logger($this->logger_name, $log_path);
    }

    /**
     * Make a GET request.
     *
     * @param string $url The URL to request.
     * @param array $args Optional. Additional arguments for the request.
     * @return mixed The response data.
     */
    public function get($url, $args = []) {
        $client = new Client();
        
        try {
            $response = $client->get($url, $args);
            return $this->handle_response($response);
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Make a POST request.
     *
     * @param string $url The URL to request.
     * @param array $args Optional. Additional arguments for the request.
     * @return mixed The response data.
     */
    public function post($url, $args = []) {
        $client = new Client();
        
        try {
            $response = $client->post($url, $args);
            return $this->handle_response($response);
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Handle the response from the HTTP request.
     *
     * @param \Psr\Http\Message\ResponseInterface $response The response object.
     * @return mixed The response data.
     */
    private function handle_response($response) {
        try {
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            // $status_code = $response->getStatusCode();
            return $data;
        } catch (\Exception $e) {
            $this->logger->error('Error handling response: ' . $e->getMessage());
            throw new \Exception('Error handling API response: ' . $e->getMessage());
        }
    }

    /**
     * Generate a URL based on the protocol and domain
     *
     * @param string $path The path to append to the URL
     * @return string The generated URL
     */
    protected function make_url(string $path): string {
        return $this->protocol . "://" . $this->domain . ":" . $this->port . $path;
    }
}