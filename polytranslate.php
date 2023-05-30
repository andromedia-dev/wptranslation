<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Polytranslate
 * Plugin URI:        https://polytranslate.net
 * Description:       Automatic translation for WordPress
 * Version:           1.0.0
 * Author:            Polytranslate
 * Author URI:        https://polytranslate.net
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       polytranslate
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

use Polytranslate\Plugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('POLYTRANSLATE_VERSION', '1.0.0');
define('POLYTRANSLATE_NONCE', 'polytranslate-nonce');
define('POLYTRANSLATE_NAME', 'polytranslate');
define('POLYTRANSLATE_ROOT_PATH', plugin_dir_path(__FILE__));
define('POLYTRANSLATE_ROOT_URL', plugin_dir_url(__FILE__));

if (defined('POLYTRANSLATE_PREMIUM_VERSION')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-includes/pluggable.php';
    if (is_plugin_active(plugin_basename(__FILE__))) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_safe_redirect(add_query_arg('deactivate', 'true', remove_query_arg('activate')));
        exit;
    }
}

if (!function_exists('polytranslate_fs')) {
    // Create a helper function for easy SDK access.
    function polytranslate_fs()
    {
        global $polytranslate_fs;

        if (!isset($polytranslate_fs)) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $polytranslate_fs = fs_dynamic_init(array(
                'id'                  => '12650',
                'slug'                => 'polytranslate',
                'premium_slug'        => 'polytranslate-premium',
                'type'                => 'plugin',
                'public_key'          => 'pk_7213ae8523c449c8cec1508433e77',
                'is_premium'          => false,
                'premium_suffix'      => 'Premium',
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => [
                    'slug'            => 'polytranslate',
                    'first-path'      => 'admin.php?page=polytranslate#/settings',
                    'contact'         => false,
                    'support'         => false,
                    'affiliation'     => false,
                    'account'         => true,
                    'addons'          => false,
                ],
                'is_live'        => true,
            ));
        }

        return $polytranslate_fs;
    }

    // Init Freemius.
    polytranslate_fs();
    // Signal that SDK was initiated.
    do_action('polytranslate_fs_loaded');
}

// load composer vendors.
if (is_readable(POLYTRANSLATE_ROOT_PATH . '/vendor_prefixed/vendor/autoload.php')) {
    require POLYTRANSLATE_ROOT_PATH . '/vendor_prefixed/vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    $namespaces = explode("\\", $class);
    if (count($namespaces) > 0 && $namespaces[0] === "Polytranslate") {
        if (count($namespaces) > 1 && $namespaces[1] === "Vendors") {
            return;
        }
        $namespaces[0] = "app";

        require_once plugin_dir_path(__FILE__) . implode("/", $namespaces) . '.php';
    }
});

add_action('plugins_loaded', function () {
    load_plugin_textdomain('polytranslate', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    $plugin = new Plugin;
    $plugin->run();
});
