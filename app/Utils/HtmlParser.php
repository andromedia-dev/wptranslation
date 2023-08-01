<?php

namespace WPTranslation\Utils;

use DOMDocument;
use DOMElement;
use DOMXPath;

class HtmlParser
{
    private $doc;
    private $xPath;

    public function __construct($content = "")
    {
        $content = "<div>" . $content . "</div>";

        libxml_use_internal_errors(true);
        $this->doc = new DOMDocument();
        $this->doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $this->xPath = new DOMXPath($this->doc);
        libxml_use_internal_errors(false);
    }

    public function extractNodesValues($doc = null, $values = [], &$index = 0)
    {
        if ($doc === null) {
            $doc = $this->doc;
        }
        $values = [];
        foreach ($doc->childNodes as $node) {

            if (in_array($node->nodeName, ["script", "style", "#cdata-section", "#comment", "svg", "path"])) {
                continue;
            }

            // attribute
            if ($node instanceof DOMElement) {
                foreach (["alt", "title"] as $attr) {
                    if ($node->hasAttribute($attr)) {
                        $attrValue = $node->getAttribute($attr);

                        // skip if attr is empty
                        if (strlen(trim($attrValue)) <= 0) {
                            continue;
                        }

                        $placeholder = "INDEX_" . $index++;

                        // count left and right spaces
                        preg_match_all('/^(\s)+/i', $attrValue, $leftSpaces);
                        preg_match_all('/(\s)+$/i', $attrValue, $rigthSpaces);

                        // trim content
                        $attrValue = trim($attrValue);

                        $values[] = [
                            "key" => $placeholder,
                            "left_spaces" => count($leftSpaces[0]) > 0 ? strlen($leftSpaces[0][0]) : 0,
                            "right_spaces" => count($rigthSpaces[0]) > 0 ? strlen($rigthSpaces[0][0]) : 0,
                            "value" => $attrValue,
                        ];

                        $node->setAttribute($attr, $placeholder);
                    }
                }
            }

            // text
            if (count($node->childNodes) > 0) {

                // recursive on children
                $values = array_merge($values, $this->extractNodesValues($node, $values, $index));
            } else {

                // skip if nodeValue is empty
                if (strlen(trim($node->nodeValue)) <= 0) {
                    continue;
                }

                $splitted = explode("\n", $node->nodeValue);
                $placeholders = [];

                foreach ($splitted as $split) {

                    // skip if split is empty
                    if (strlen(trim($split)) <= 0) {
                        $placeholders[] = "";
                        continue;
                    }

                    $key = "INDEX_" . $index++;
                    $nodeValue = $split;

                    // detect wp shortcode
                    if (preg_match_all("@(\[\/?[^<>&/\[\]]+\])@", $nodeValue, $matches) > 0) {

                        // translate only shortcode with content
                        if (count($matches[0]) !== 2) {
                            continue;
                        }
                        $nodeValue = str_replace($matches[0][0], "", $nodeValue);
                        $nodeValue = str_replace($matches[0][1], "", $nodeValue);
                        $placeholder = $matches[0][0] . $key . $matches[0][1];
                    } else {
                        $placeholder = $key;
                    }

                    // count left and right spaces
                    preg_match_all('/^(\s)+/i', $nodeValue, $leftSpaces);
                    preg_match_all('/(\s)+$/i', $nodeValue, $rigthSpaces);

                    // trim content
                    $nodeValue = trim($nodeValue);

                    $values[] = [
                        "key" => $key,
                        "left_spaces" => count($leftSpaces[0]) > 0 ? strlen($leftSpaces[0][0]) : 0,
                        "right_spaces" => count($rigthSpaces[0]) > 0 ? strlen($rigthSpaces[0][0]) : 0,
                        "value" => $nodeValue,
                    ];

                    $placeholders[] = $placeholder;
                }

                $node->nodeValue = implode("\n", $placeholders);
            }
        }

        return $values;
    }

    public function getHtml()
    {
        // fix <li> tag
        $nodes = $this->xPath->query('/html/body//*[not(node())]');
        foreach ($nodes as $nodes__value) {
            $nodes__value->nodeValue = '';
        }
        $html = $this->doc->saveHTML();
        $html = preg_replace(['/^<!DOCTYPE.*<body><div>/s', '/<\/div><\/body><\/html>$/'], "", $html);
        return html_entity_decode($html);
    }
}
