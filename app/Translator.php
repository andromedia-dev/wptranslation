<?php

namespace WPTranslation;

use PLL_Admin_Sync;
use WPTranslation\Api\GoogleTranslate;
use WPTranslation\Utils\Language;
use WPTranslation\Utils\Settings;
use WPTranslation\Vendors\Illuminate\Support\Str;

if (class_exists("PLL_Admin_Sync")) {

    class Translator extends PLL_Admin_Sync
    {

        static private $instance = null;

        public function __construct(&$polylang)
        {
            parent::__construct($polylang);

            // translate post content
            add_filter('use_block_editor_for_post', [$this, "live_translate"], 5001, 2);

            // translate post title
            add_filter('plt_translate_post_title', [$this, "translate_post_title"], 10, 3);

            // translate post content
            add_filter('plt_translate_post_content', [$this, "translate_post_content"], 10, 3);

            // translate post excerpt
            add_filter('plt_translate_post_excerpt', [$this, "translate_post_excerpt"], 10, 3);

            // translate post meta
            add_filter('pll_translate_post_meta', [$this, "translate_post_meta"], 10, 4);
        }

        static function make(&$polylang)
        {
            if (self::$instance === null) {
                self::$instance = new self($polylang);
            }

            return self::$instance;
        }

        public function live_translate($is_block_editor, $post)
        {
            if (!empty($post) && isset($GLOBALS['pagenow'], $_GET['from_post'], $_GET['new_lang']) && 'post-new.php' === $GLOBALS['pagenow'] && $this->model->is_translated_post_type($post->post_type)) {
                check_admin_referer('new-post-translation');

                $from_post_id = (int) $_GET['from_post'];
                $parent_post = get_post($from_post_id);
                $parent_post_lang = $this->model->post->get_language($from_post_id);
                $post_lang = $this->model->get_language(sanitize_key($_GET['new_lang']));

                if ($this->canTranslatePost($post_lang->slug)) {

                    // copy post content
                    $this->copyPostContent($parent_post, $post);

                    // translate the post content
                    $this->translatePost($post, $post_lang, $parent_post_lang);
                }
            }

            return $is_block_editor;
        }

        public function translate_post_meta($value, $key, $target_lang, $from_lang)
        {
            if (!in_array($key, [
                "_thumbnail_id",
            ]) && $this->canTranslatePost($target_lang)) {

                // detect if yoast post meta
                if (in_array($key, [
                    "_yoast_wpseo_focuskw",
                    "_yoast_wpseo_metadesc",
                    "_yoast_wpseo_title",
                    "_yoast_wpseo_bctitle",
                    "_yoast_wpseo_opengraph-title",
                    "_yoast_wpseo_opengraph-description",
                    "_yoast_wpseo_twitter-title",
                    "_yoast_wpseo_twitter-description",
                ])) {
                    if (Settings::get("translation.yoast.enabled", false)) {
                        $service = Settings::get("service", "google-translate-free");
                        $value = $this->translateContent($service, $value, $target_lang, $from_lang, "yoast");
                    }
                } else if (Settings::get("translation.post_meta.enabled", false)) {
                    $service = Settings::get("service", "google-translate-free");
                    $value = $this->translateContent($service, $value, $target_lang, $from_lang, "html");
                }
            }

            return $value;
        }

        public function translate_post_title($value, $target_lang_slug, $from_lang_slug)
        {
            if ($this->canTranslatePost($target_lang_slug)) {
                $target_lang = $this->getTranslateLang($target_lang_slug);
                $from_lang = $this->getTranslateLang($from_lang_slug);

                $service = Settings::get("service", "google-translate-free");

                $value = $this->translateContent($service, $value, $target_lang, $from_lang, "text");
            }

            return $value;
        }

        public function translate_post_content($value, $target_lang, $from_lang)
        {
            if ($this->canTranslatePost($target_lang)) {
                $target_lang = $this->getTranslateLang($target_lang);
                $from_lang = $this->getTranslateLang($from_lang);

                $service = Settings::get("service", "google-translate-free");

                return $this->translateContent($service, $value, $target_lang, $from_lang, "html");
            }
            return $value;
        }

        public function translate_post_excerpt($value, $target_lang, $from_lang)
        {
            if ($this->canTranslatePost($target_lang)) {
                $target_lang = $this->getTranslateLang($target_lang);
                $from_lang = $this->getTranslateLang($from_lang);

                $service = Settings::get("service", "google-translate-free");

                return $this->translateContent($service, $value, $target_lang, $from_lang, "text");
            }
            return $value;
        }

        public function duplicatePost($from_post_id, $new_lang, $refresh = false)
        {
            // verify that polylang functions exists
            if (!function_exists("pll_get_post_translations") || !function_exists("pll_save_post_translations")) {
                return null;
            }

            $from_post = get_post($from_post_id);
            $from_post_lang = $this->model->post->get_language($from_post_id);
            $lang = $this->model->get_language(sanitize_key($new_lang));

            if ($from_post && $lang && $from_post_lang && $from_post_lang->term_id !== $lang->term_id && $this->model->is_translated_post_type($from_post->post_type)) {

                if (!$this->canDuplicatePost($from_post, $lang->slug, $refresh)) {
                    return null;
                }

                if (!$this->canTranslatePost($lang->slug)) {
                    return null;
                }

                if ($refresh && $this->hasTranslation($from_post_id, $lang->slug)) {
                    $post = null;

                    $translations = pll_get_post_translations($from_post_id);

                    if (isset($translations[$lang->slug])) {
                        $post = get_post($translations[$lang->slug]);
                    }

                    if (!$post) {
                        return null;
                    }
                } else {
                    // create default post
                    $post = get_default_post_to_edit($from_post->post_type, true);

                    // associate language
                    pll_set_post_language($post->ID, $lang->slug);
                }

                // copy taxonomies and post metas
                $this->taxonomies->copy($from_post_id, $post->ID, $lang->slug);
                $this->post_metas->copy($from_post_id, $post->ID, $lang->slug);

                // add sticky if parent post is sticky
                if (is_sticky($from_post_id)) {
                    stick_post($post->ID);
                }

                // associate parent post
                $this->associatePostParent($from_post, $post);

                // copy post properties
                $this->copyPostProperties($from_post, $post);

                // copy post content
                $this->copyPostContent($from_post, $post);

                // associate translations
                $this->addPostTranslation($from_post, $post);

                // translate the post content
                $this->translatePost($post, $lang, $from_post_lang);

                // insert the post
                wp_insert_post($post, true);

                return $post;
            }

            return null;
        }

        private function addPostTranslation($from_post, $post)
        {
            $translations = pll_get_post_translations($from_post->ID);
            $lang = pll_get_post_language($post->ID, 'slug');
            $translations[$lang] = $post->ID;
            pll_save_post_translations($translations);
        }

        private function associatePostParent($from_post, &$post)
        {
            $parent_post_id = wp_get_post_parent_id($from_post->ID);
            $lang = pll_get_post_language($post->ID, 'slug');
            if ($parent_post_id && ($parent = $this->model->post->get_translation($parent_post_id, $lang))) {
                $post->post_parent = $parent;
            }
        }

        private function copyPostContent($from_post, &$post)
        {
            $post->post_title = $from_post->post_title;
            $post->post_content = $from_post->post_content;
            $post->post_excerpt = $from_post->post_excerpt;
        }

        private function copyPostProperties($from_post, &$post)
        {
            $properties = ['post_status', 'menu_order', 'comment_status', 'ping_status'];
            foreach ($properties as $property) {
                $post->$property = $from_post->$property;
            }

            // Copy the date only if the synchronization is activated
            if (in_array('post_date', $this->options['sync'])) {
                $post->post_date = $from_post->post_date;
                $post->post_date_gmt = $from_post->post_date_gmt;
            }
        }

        private function hasTranslation($post_id, $lang)
        {
            $lang = $this->model->get_language(sanitize_key($lang));
            $translations = pll_get_post_translations($post_id);
            if (isset($translations[$lang->slug])) {
                $translated_post = get_post($translations[$lang->slug]);

                return $translated_post->post_status !== "auto-draft";
            }

            return false;
        }

        private function translatePost(&$post, $target_lang, $source_lang = null)
        {
            if (!empty($post) && $this->model->is_translated_post_type($post->post_type)) {

                $post->post_title = apply_filters("plt_translate_post_title", $post->post_title, $target_lang->slug, $source_lang->slug);

                $post->post_content = apply_filters("plt_translate_post_content", $post->post_content, $target_lang->slug, $source_lang->slug);

                $post->post_excerpt = apply_filters("plt_translate_post_excerpt", $post->post_excerpt, $target_lang->slug, $source_lang->slug);
            }
        }

        private function translateContent($service, $content, $target, $source = null, $mode = "text")
        {
            switch ($service) {
                case "google-translate-free":
                    $api = new GoogleTranslate;
                    if ($mode === "text") {
                        return $api->translate($content, $target, $source);
                    } else if ($mode === "html") {
                        return $api->translateHtml($content, $target, $source);
                    } else if ($mode === "yoast") {
                        return $this->translateYoastContent($service, $content, $target, $source);
                    }
                    break;
            }


            return $content;
        }

        private function translateYoastContent($service, $content, $target, $source)
        {
            switch ($service) {
                case "google-translate-free":
                    $api = new GoogleTranslate;

                    $values = [];
                    $index = 1;

                    $content = preg_replace_callback("/(%%[\w]+%%|[\w\s]+)/", function ($matches) use (&$values, &$index) {

                        // detect yoast variable
                        if (Str::startsWith($matches[0], "%%") && Str::endsWith($matches[0], "%%")) {
                            return $matches[0];
                        }

                        // skip if nodeValue is empty
                        if (strlen(trim($matches[0])) <= 0) {
                            return $matches[0];
                        }

                        $key = "INDEX_" . $index++;

                        // count left and right spaces
                        preg_match_all('/^(\s)+/i', $matches[0], $leftSpaces);
                        preg_match_all('/(\s)+$/i', $matches[0], $rigthSpaces);

                        $values[] = [
                            "key" => $key,
                            "left_spaces" => count($leftSpaces[0]) > 0 ? strlen($leftSpaces[0][0]) : 0,
                            "right_spaces" => count($rigthSpaces[0]) > 0 ? strlen($rigthSpaces[0][0]) : 0,
                            "value" => trim($matches[0]),
                        ];

                        return $key;
                    }, $content);

                    $texts = array_map(function ($val) {
                        return $val["value"];
                    }, $values);

                    $texts = $api->translate($texts, $target, $source);

                    $values = array_map(function ($val, $text) {
                        $val["value"] = $text;
                        return $val;
                    }, $values, $texts);

                    // reorder values to avoid miss remplacement errors
                    usort($values, function ($a, $b) {
                        $aKeyIndex = intval(str_replace("INDEX_", "", $a["key"]));
                        $bKeyIndex = intval(str_replace("INDEX_", "", $b["key"]));
                        return $aKeyIndex < $bKeyIndex;
                    });

                    // replace placeholder with translated texts
                    foreach ($values as $value) {
                        if ($value["key"]) {
                            $search = $value["key"];
                            $replacement = str_repeat(" ", $value["left_spaces"] ?? 0) . $value["value"] . str_repeat(" ", $value["right_spaces"] ?? 0);
                            $content = str_replace($search, $replacement, $content);
                        }
                    }

                    return $content;
                    break;
            }

            return $content;
        }

        private function getTranslateLang($slug)
        {
            if (empty($slug)) {
                return null;
            }
            $slug = explode("-", $slug)[0];

            $service = Settings::get("service", "google-translate-free");
            return Language::getLanguageForService($service, $slug);
        }

        private function canDuplicatePost($parent_post, $lang, $refresh = false)
        {
            return $refresh || !$this->hasTranslation($parent_post->ID, $lang);
        }

        private function canTranslatePost($slug)
        {
            return $this->getTranslateLang($slug) !== null;
        }
    }
}
