<?php
/**
 * Custom rest api for website
 */

/**
 * Register custom route
 */
function vr_register_custom_route () {
    // Get current/ last matches
    // register_rest_route('vietqr/v1', '/fixtures', [
    //     'methods' => 'GET',
    //     'callback' => 'get_fixtures',
    //     'permission_callback' => 'verify_rest_nonce'
    // ]);
}
add_action('rest_api_init', 'vr_register_custom_route');

/**
 * Verify rest nonce
 */
function verify_rest_nonce(WP_REST_Request $request) {
    $nonce = $request->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error(
            'rest_forbidden', 
            esc_html__('You do not have permission to access this endpoint.'), 
            ['status' => 403]
        );
    }
    return true;
}