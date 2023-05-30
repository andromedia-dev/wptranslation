<?php

namespace Polytranslate\Utils;

abstract class Language
{
    static public function getLanguageForService($service, $lang)
    {
        $languages = [];
        switch ($service) {
            case "google-translate-free":
                $languages = self::googleLanguages();
                break;
        }

        if (!isset($languages[$lang])) {
            return null;
        }

        return $languages[$lang];
    }

    static private function googleLanguages()
    {
        return [
            "af" => "af",
            "am" => "am",
            "ar" => "ar",
            "as" => "as",
            "az" => "az",
            "be" => "be",
            "bg" => "bg",
            "bn" => "bn",
            "bs" => "bs",
            "ca" => "ca",
            "ceb" => "ceb",
            "ku" => "ku",
            "cs" => "cs",
            "cy" => "cy",
            "da" => "da",
            "de" => "de",
            "el" => "el",
            "en" => "en",
            "eo" => "eo",
            "es" => "es",
            "et" => "et",
            "eu" => "eu",
            "fa" => "fa",
            "fi" => "fi",
            "fr" => "fr",
            "fy" => "fy",
            "gd" => "gd",
            "gl" => "gl",
            "gu" => "gu",
            "hi" => "hi",
            "hr" => "hr",
            "hu" => "hu",
            "hy" => "hy",
            "id" => "id",
            "is" => "is",
            "it" => "it",
            "ja" => "ja",
            "jv" => "jw",
            "ka" => "ka",
            "kk" => "kk",
            "km" => "km",
            "kn" => "kn",
            "ko" => "ko",
            "lo" => "lo",
            "lt" => "lt",
            "lv" => "lv",
            "mk" => "mk",
            "ml" => "ml",
            "mn" => "mn",
            "mr" => "mr",
            "ms" => "ms",
            "my" => "my",
            "nb" => "no",
            "ne" => "ne",
            "nl" => "nl",
            "nn" => "no",
            "pa" => "pa",
            "pl" => "pl",
            "ps" => "ps",
            "pt" => "pt",
            "ro" => "ro",
            "ru" => "ru",
            "si" => "si",
            "sk" => "sk",
            "sl" => "sl",
            "sd" => "sd",
            "so" => "so",
            "sq" => "sq",
            "sr" => "sr",
            "su" => "su",
            "sv" => "sv",
            "sw" => "sw",
            "ta" => "ta",
            "te" => "te",
            "th" => "th",
            "tl" => "tl",
            "tr" => "tr",
            "tt" => "tt",
            "ug" => "ug",
            "uk" => "uk",
            "ur" => "ur",
            "uz" => "uz",
            "vi" => "vi",
            "zh" => "zh-CN"
        ];
    }
}
