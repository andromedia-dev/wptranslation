<?php

/**
 * @wordpress-plugin
 * Plugin Name:       WPTranslation
 * Plugin URI:        https://wptranslation.net
 * Description:       Automatic translation for WordPress
 * Version:           1.0.0
 * Author:            WPTranslation
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wptranslation
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

use WPTranslation\Plugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('WPTRANSLATION_VERSION', '1.0.0');
define('WPTRANSLATION_NONCE', 'wptranslation-nonce');
define('WPTRANSLATION_NAME', 'wptranslation');
define('WPTRANSLATION_ROOT_PATH', plugin_dir_path(__FILE__));
define('WPTRANSLATION_ROOT_URL', plugin_dir_url(__FILE__));

if (!function_exists('wptranslation_fs')) {
    // Create a helper function for easy SDK access.
    function wptranslation_fs()
    {
        global $wptranslation_fs;

        if (!isset($wptranslation_fs)) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $wptranslation_fs = fs_dynamic_init(array(
                'id'                  => '12650',
                'slug'                => 'wptranslation',
                'premium_slug'        => 'wptranslation-premium',
                'type'                => 'plugin',
                'public_key'          => 'pk_7213ae8523c449c8cec1508433e77',
                'is_premium'          => false,
                'premium_suffix'      => 'Premium',
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'is_org_compliant'    => true,
                'menu'                => [
                    'slug'            => 'wptranslation',
                    'first-path'      => 'admin.php?page=wptranslation#/settings',
                    'contact'         => false,
                    'support'         => false,
                    'affiliation'     => false,
                    'account'         => true,
                    'addons'          => false,
                ],
                'is_live'        => true,
            ));
        }

        return $wptranslation_fs;
    }

    // Init Freemius.
    wptranslation_fs();
    // Signal that SDK was initiated.
    do_action('wptranslation_fs_loaded');
}

// load composer vendors.
if (is_readable(WPTRANSLATION_ROOT_PATH . '/vendor_prefixed/vendor/autoload.php')) {
    require WPTRANSLATION_ROOT_PATH . '/vendor_prefixed/vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    $namespaces = explode("\\", $class);
    if (count($namespaces) > 0 && $namespaces[0] === "WPTranslation") {
        if (count($namespaces) > 1 && $namespaces[1] === "Vendors") {
            return;
        }
        $namespaces[0] = "app";

        require_once plugin_dir_path(__FILE__) . implode("/", $namespaces) . '.php';
    }
});

add_action('plugins_loaded', function () {
    load_plugin_textdomain('wptranslation', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    $plugin = new Plugin;
    $plugin->run();
});
