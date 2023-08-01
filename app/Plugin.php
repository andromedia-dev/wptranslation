<?php

namespace WPTranslation;

use WPTranslation\Utils\Detector;

class Plugin
{
    protected $translator;

    public function run()
    {
        global $polylang;

        if (Detector::polylangIsActive()) {
            $this->translator = Translator::make($polylang);
        }

        if (function_exists("is_admin") && is_admin()) {
            $this->admin();
        }
    }

    private function admin()
    {
        // define menu
        add_action('admin_menu', function () {
            add_menu_page(
                'WPTranslation',
                'WPTranslation',
                'manage_options',
                WPTRANSLATION_NAME,
                [$this, "display"],
                WPTRANSLATION_ROOT_URL . 'public/img/logo_sidebar.png',
                30
            );

            add_submenu_page(WPTRANSLATION_NAME, 'wptranslation', __('Bulk translation', 'wptranslation'), "manage_options", WPTRANSLATION_NAME, [$this, "display"]);

            add_submenu_page(WPTRANSLATION_NAME, 'wptranslation', __('Settings', 'wptranslation'), "manage_options", WPTRANSLATION_NAME . "#/settings", [$this, "display"]);
        });

        wp_enqueue_style('wptranslation-app',  WPTRANSLATION_ROOT_URL . 'public/css/wptranslation.css', [], WPTRANSLATION_VERSION);

        // meta box
        add_action('add_meta_boxes', [$this, "metaBox"]);

        require_once plugin_dir_path(__FILE__) . '../routes/ajax.php';
    }

    public function display()
    {
        $data = [
            "polylang_active" => Detector::polylangIsActive(),
            "ajaxurl" => admin_url('admin-ajax.php'),
            "nonce" => wp_create_nonce(WPTRANSLATION_NONCE),
            "public_path" => WPTRANSLATION_ROOT_URL . '/public',
            "admin_url" =>  admin_url(),
        ];
        wp_enqueue_script('wptranslation-app', WPTRANSLATION_ROOT_URL . 'public/js/wptranslation.js', ['wp-i18n'], WPTRANSLATION_VERSION, true);
        wp_set_script_translations('wptranslation-app', WPTRANSLATION_NAME, WPTRANSLATION_ROOT_PATH . 'languages/');
        wp_add_inline_script('wptranslation-app', 'var wptranslation = ' . json_encode($data) . ';', "before");

        echo "<div id='wptranslation-app' class='wptranslation-app'></div>";
    }

    public function metaBox()
    {
        $post_types = get_post_types();
        foreach ($post_types as $post_type) {
            add_meta_box(
                WPTRANSLATION_NAME . '-box-id',
                'WPTranslation',
                function ($post) {
?>
                <div id='wptranslation-meta-box'>
                    <a class="bulk-mode" href="<?php echo admin_url('admin.php?page=' . WPTRANSLATION_NAME); ?>" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5 mr-2 -ml-1">
                            <path d="M7.75 2.75a.75.75 0 00-1.5 0v1.258a32.987 32.987 0 00-3.599.278.75.75 0 10.198 1.487A31.545 31.545 0 018.7 5.545 19.381 19.381 0 017 9.56a19.418 19.418 0 01-1.002-2.05.75.75 0 00-1.384.577 20.935 20.935 0 001.492 2.91 19.613 19.613 0 01-3.828 4.154.75.75 0 10.945 1.164A21.116 21.116 0 007 12.331c.095.132.192.262.29.391a.75.75 0 001.194-.91c-.204-.266-.4-.538-.59-.815a20.888 20.888 0 002.333-5.332c.31.031.618.068.924.108a.75.75 0 00.198-1.487 32.832 32.832 0 00-3.599-.278V2.75z"></path>
                            <path fill-rule="evenodd" d="M13 8a.75.75 0 01.671.415l4.25 8.5a.75.75 0 11-1.342.67L15.787 16h-5.573l-.793 1.585a.75.75 0 11-1.342-.67l4.25-8.5A.75.75 0 0113 8zm2.037 6.5L13 10.427 10.964 14.5h4.073z" clip-rule="evenodd"></path>
                        </svg> <?php echo __('Bulk mode', 'wptranslation'); ?></a>
                    <a class="settings" href="<?php echo admin_url('admin.php?page=' . WPTRANSLATION_NAME . '#/settings'); ?>" type="button">
                        <?php echo __('Settings', 'wptranslation'); ?></a>
                </div>
<?php
                },
                $post_type,
                'side',
                'high',
                ['__block_editor_compatible_meta_box' => true]
            );
        }
    }
}
