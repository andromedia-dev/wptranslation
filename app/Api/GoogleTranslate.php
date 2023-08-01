<?php

namespace WPTranslation\Api;

use DOMDocument;
use DOMElement;
use WPTranslation\Utils\HtmlParser;

class GoogleTranslate
{
    static $maxCharacters = 2000;
    private $endpoint;

    function __construct()
    {
        $this->endpoint = "https://www.google.com/async/translate?yv=3";
    }

    public function translate($content, $target, $source = null)
    {
        $params = [
            "translate",
            "id:" . time() . "100",
            "qc:true",
            "ac:false",
            "_id:tw-async-translate",
            "_pms:s",
            "_fmt:pc"
        ];

        if (!self::isAcceptedLanguage($target)) {
            return $content;
        }
        $params[] = "tl:" . $target;

        if (isset($source) && self::isAcceptedLanguage($source)) {
            $params[] = "sl:" . $source;
        } else {
            $params[] = "sl:auto";
        }

        $args = [];
        $texts = $content;
        if (!is_array($content)) {
            $texts = [$content];
        }

        $results = [];

        $chunks = $this->createChunks($texts);

        $args["headers"] = [
            "Content-Type" => "application/x-www-form-urlencoded;charset=UTF-8",
            "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36"
        ];

        foreach ($chunks as $chunk) {

            if (!$chunk["concat"]) {
                $itemsChunk = [$chunk["items"]];
            } else {
                $itemsChunk = $chunk["items"];
            }

            $translations = [];

            foreach ($itemsChunk as $items) {
                $chunkParams = $params;

                if (is_array($items)) {
                    $encodedItems = [];
                    for ($i = 0; $i < count($items); $i++) {
                        $encodedItems[] = rawurlencode($items[$i]);
                    }
                    $chunkParams[] = "st:" . implode('%0a', $encodedItems);
                } else {
                    $chunkParams[] = "st:" . rawurlencode($items);
                }
                $args["body"] = "async=" . implode(",", $chunkParams);

                // Call the API
                $response = wp_remote_post(
                    $this->endpoint,
                    $args
                );

                // Update the option only if we got a response
                if (is_wp_error($response)) {
                    return $content;
                }

                $result = wp_remote_retrieve_body($response);

                preg_match('/<span (?:dir="rtl" )?id="tw-answ-target-text">(.*?)<\/span>/is', $result, $matches);

                if (count($matches) > 1) {
                    $translations = array_merge($translations, explode("\n", html_entity_decode($matches[1])));
                }
            }

            if ($chunk["concat"]) {
                $translations = [implode(" ", $translations)];
            }

            $results = array_merge($results, $translations);
        }

        if (count($results) <= 0) {
            return $content;
        }

        if (!is_array($content)) {
            return $results[0];
        } else {
            return $results;
        }

        return $content;
    }

    public function translateHtml($content, $target, $source = null)
    {
        $parser = new HtmlParser($content);

        $values = $parser->extractNodesValues();

        $content = $parser->getHtml();

        $texts = array_map(function ($val) {
            return $val["value"];
        }, $values);

        $texts = $this->translate($texts, $target, $source);

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
    }

    private function createChunks($texts)
    {
        $chunks = [];

        $actualNumberOfCharacters = 0;
        $first = true;
        for ($i = 0; $i < count($texts); $i++) {
            $len = strlen($texts[$i]);
            $actualNumberOfCharacters += $len;
            if ($first || $actualNumberOfCharacters >= self::$maxCharacters) {
                $actualNumberOfCharacters = $len;
                $chunks[] = [
                    "concat" => false,
                    "items" => []
                ];
                $first = false;
            }

            if ($len < self::$maxCharacters) {
                $chunks[count($chunks) - 1]["items"][] = $texts[$i];
            } else {
                // split string
                $chunks[count($chunks) - 1] = [
                    "concat" => true,
                    "items" => str_split($texts[$i], self::$maxCharacters)
                ];
            }
        }

        return $chunks;
    }

    static public function isAcceptedLanguage($target)
    {
        return in_array($target, [
            "af",
            "sq",
            "de",
            "am",
            "en",
            "ar",
            "hy",
            "as",
            "ay",
            "az",
            "bm",
            "eu",
            "bn",
            "bho",
            "be",
            "my",
            "bs",
            "bg",
            "ca",
            "ceb",
            "ny",
            "zh-CN",
            "si",
            "ko",
            "co",
            "ht",
            "hr",
            "da",
            "dv",
            "doi",
            "es",
            "eo",
            "et",
            "ee",
            "fi",
            "fr",
            "fy",
            "gd",
            "gl",
            "cy",
            "ka",
            "el",
            "gn",
            "gu",
            "ha",
            "haw",
            "iw",
            "hi",
            "hmn",
            "hu",
            "ig",
            "ilo",
            "id",
            "ga",
            "is",
            "it",
            "ja",
            "jw",
            "kn",
            "kk",
            "km",
            "rw",
            "ky",
            "gom",
            "kri",
            "ku",
            "ckb",
            "lo",
            "la",
            "lv",
            "ln",
            "lt",
            "lg",
            "lb",
            "mk",
            "mai",
            "ms",
            "ml",
            "mg",
            "mt",
            "mi",
            "mr",
            "mni-Mtei",
            "lus",
            "mn",
            "nl",
            "ne",
            "no",
            "or",
            "om",
            "ug",
            "uz",
            "ps",
            "pa",
            "fa",
            "tl",
            "pl",
            "pt",
            "qu",
            "ro",
            "ru",
            "sm",
            "sa",
            "nso",
            "sr",
            "st",
            "sn",
            "sd",
            "sk",
            "sl",
            "so",
            "su",
            "sv",
            "sw",
            "tg",
            "ta",
            "tt",
            "cs",
            "te",
            "th",
            "ti",
            "ts",
            "tr",
            "tk",
            "ak",
            "uk",
            "ur",
            "vi",
            "xh",
            "yi",
            "yo",
            "zu"
        ]);
    }
}
