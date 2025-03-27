<?php
/**
 * Plugin Name: VietQR VN Payment
 * Description: VietQR VN Payment
 * Version: 1.1.1
 * Author: Khoi Tran
 * Text Domain: vietqr
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define constants.
define('VIETQR_VERSION', '1.1.0');
define('VIETQR_PATH', plugin_dir_path(__FILE__));
define('VIETQR_URL', rtrim(plugin_dir_url(__FILE__), '/'));
define('VIETQR_BASENAME', plugin_basename(__FILE__));
define('VIETQR_FILE', __FILE__);
define('VIETQR_TEXTDOMAIN', 'vietqr');

// Load text domain for translations
function vietqr_load_textdomain() {
    load_plugin_textdomain(VIETQR_TEXTDOMAIN, false, basename(VIETQR_PATH) . '/languages');
}
add_action('plugins_loaded', 'vietqr_load_textdomain');

// Composer autoload
require_once VIETQR_PATH . 'build/vendor/scoper-autoload.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'VietQR\\';
    $base_dir = VIETQR_PATH . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Required
foreach (glob(VIETQR_PATH . 'functions/*.php') as $file) {
    require_once $file;
}

/**
 * Plugin activation function.
 */
function vietqr_activate()
{
    // Activate
    vr_create_bank_accounts_table();
    vr_create_transactions_table();
}
register_activation_hook(__FILE__, 'vietqr_activate');

/**
 * Plugin deactivation function.
 */
function vietqr_deactivate()
{
    // Deactivate
}
register_deactivation_hook(__FILE__, 'vietqr_deactivate');

/**
 * Plugin uninstall function.
 */
function vietqr_uninstall()
{
    // Uninstall
    vr_delete_transactions_table();
    vr_delete_bank_accounts_table();
}
register_uninstall_hook(__FILE__, 'vietqr_uninstall');

// Load .env
$dotenv = new VietQR\Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__.'/.env');

// Load global variables
global $errors, $successes;
$errors = VietQR\Support\Flash::get_errors();
$successes = VietQR\Support\Flash::get_successes();