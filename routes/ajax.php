<?php

// requests

use WPTranslation\Controllers\LanguagesController;
use WPTranslation\Controllers\SettingsController;
use WPTranslation\Controllers\TranslateController;
use WPTranslation\Controllers\WordpressController;

// common
add_action("wp_ajax_wptranslation_languages", new LanguagesController);
add_action("wp_ajax_wptranslation_wp_post_types", [new WordpressController, 'postTypes']);
add_action("wp_ajax_wptranslation_wp_categories", [new WordpressController, 'categories']);
add_action("wp_ajax_wptranslation_wp_product_categories", [new WordpressController, 'productCategories']);
add_action("wp_ajax_wptranslation_wp_users", [new WordpressController, 'users']);

// translation
add_action("wp_ajax_wptranslation_translate_get_posts", [new TranslateController, 'index']);

// settings
add_action("wp_ajax_wptranslation_settings_index", [new SettingsController, 'index']);
add_action("wp_ajax_wptranslation_settings_update", [new SettingsController, 'update']);
