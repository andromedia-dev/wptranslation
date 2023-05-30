<?php

// requests

use Polytranslate\Controllers\LanguagesController;
use Polytranslate\Controllers\SettingsController;
use Polytranslate\Controllers\TranslateController;
use Polytranslate\Controllers\WordpressController;

// common
add_action("wp_ajax_polytranslate_languages", new LanguagesController);
add_action("wp_ajax_polytranslate_wp_post_types", [new WordpressController, 'postTypes']);
add_action("wp_ajax_polytranslate_wp_categories", [new WordpressController, 'categories']);
add_action("wp_ajax_polytranslate_wp_product_categories", [new WordpressController, 'productCategories']);
add_action("wp_ajax_polytranslate_wp_users", [new WordpressController, 'users']);

// translation
add_action("wp_ajax_polytranslate_translate_get_posts", [new TranslateController, 'index']);

// settings
add_action("wp_ajax_polytranslate_settings_index", [new SettingsController, 'index']);
add_action("wp_ajax_polytranslate_settings_update", [new SettingsController, 'update']);
