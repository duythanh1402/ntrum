<?php

/**
 * Format date from current format to another format.
 * 
 * @param string $date Date string
 * @param string $curent_format Format of the date. Default is 'Y-m-d'.
 * @param string $new_format New format of the date. Default is 'd/m/Y'.
 * @return string Formatted date.
 */
function vr_format_date( $date, $current_format = 'Y-m-d', $new_format = 'd/m/Y' ) {
    $date = DateTime::createFromFormat( $current_format, $date );
    return $date->format( $new_format );
}

/**
 * Get templates.
 *
 * @param string $template_name Name of the template file.
 * @param string $path Path to the template file. Default is 'public/templates/'.
 * @param array $args Arguments to pass to the template.
 * @return string|bool Template file path on success, false on failure.
 */
function vr_get_template( $template_name, $path = 'public/templates/', $args = array() ) {
    // Path to the template file inside the theme
    // In your theme create folder called vietqr-vn-payment and place the file php
    $theme_template_path = get_stylesheet_directory() . '/vietqr-vn-payment/' . $template_name . '.php';

    // Check if the template file exists in the theme directory
    if ( file_exists( $theme_template_path ) ) {
        extract( $args ); // Extract arguments to be used in the template
        include $theme_template_path; // Include the template file
        return $theme_template_path; // Return the template path
    }

    // Path to the template file inside the plugin
    $plugin_template_path = VIETQR_PATH . $path . $template_name . '.php';

    // Check if the template file exists in the plugin directory
    if ( file_exists( $plugin_template_path ) ) {
        extract( $args ); // Extract arguments to be used in the template
        include $plugin_template_path; // Include the template file
        return $plugin_template_path; // Return the template path
    }

    // Return false if the template file does not exist
    return false;
}

/**
 * Get admin templates.
 * 
 * @param string $template_name Name of the template file.
 * @param array $args Arguments to pass to the template.
 * @return string|bool Template file path on success, false on failure.
 */
function vr_get_admin_template( $template_name, $args = array() ) {
    return vr_get_template( $template_name, 'admin/templates/', $args );
}

/**
 * Get public templates.
 * 
 * @param string $template_name Name of the template file.
 * @param array $args Arguments to pass to the template.
 * @return string|bool Template file path on success, false on failure.
 */
function vr_get_public_template( $template_name, $args = array() ) {
    return vr_get_template( $template_name, 'public/templates/', $args );
}

/**
 * Reload the current page with Javascript. Exit immediately after.
 */
function vr_reload() {
    echo '<script>location.reload();</script>';
    exit;
}

/**
 * Javascript redirect.
 * 
 * @param string $url URL to redirect to.
 */
function vr_redirect( $url ) {
    echo "<script>window.location.href = '$url';</script>";
    exit;
}

/**
 * Debug function.
 */
function vr_debug($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * Format any currency to number
 *
 * @param $number
 * @return int
 */
function vr_convert_currency_to_number($number) {
	$number = str_replace(",", "", $number);
	$number = strtok($number, ".");
	$number = intval($number);

	return $number;
}

/**
 * Convert timestamp to custom format
 *
 * @param $timestamp
 * @param $format
 * @return string
 */
function vr_convert_timestamp_to_date($timestamp, $format = 'd/m/Y h:i:s') {
	// Convert timestamp to date
	$date = date($format, $timestamp);
	return $date;
}

/**
 * Get current domain name
 *
 * @return string
 */
function vr_get_domain () {
	return $_SERVER['HTTP_HOST'];
}