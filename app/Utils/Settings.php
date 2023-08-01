<?php

namespace WPTranslation\Utils;

use WPTranslation\Vendors\Illuminate\Support\Arr;

class Settings
{
    const OPTION_NAME = "wptranslation_settings";

    static private $data;
    static private $initialized = false;

    static private $defaults = [
        "service" => "google-translate-free",
        "translation" => [
            "post_meta" => [
                "enabled" => false,
            ],
            "yoast" => [
                "enabled" => true,
            ],
        ],
        "rewrite" => [
            "title" => [
                "enabled" => false,
            ]
        ],
    ];

    static public function get($key = null, $default = null)
    {
        if (!self::$initialized) {
            self::$data = get_option(self::OPTION_NAME, []);
            self::$data = array_replace_recursive(self::$defaults, self::$data);
            self::$initialized = true;
        }

        if ($key === null) {
            return self::$data;
        }
        return Arr::get(self::$data, $key, $default);
    }

    static public function update($value, $key = null)
    {
        $data = self::get();
        if ($key === null) {
            $data = $value;
        }
        Arr::set($data, $key, $value);

        self::$data = $data;

        return self::$data;
    }

    static public function save()
    {
        return update_option(self::OPTION_NAME, self::get(), true);
    }
}
