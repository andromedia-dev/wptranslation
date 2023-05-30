<?php

namespace Polytranslate\Controllers;

class WordpressController extends Controller
{
    public function users()
    {
        $this->verifyNonce();

        $this->response(array_map(function ($user) {

            return [
                'id' => $user->ID,
                'name' => $user->display_name,
            ];
        }, get_users()));
    }

    public function categories()
    {
        $this->verifyNonce();

        $this->response(array_values(get_categories(["hide_empty" => true])));
    }

    public function productCategories()
    {
        $this->verifyNonce();

        $this->response(array_values(get_categories(["taxonomy" => "product_cat", "hide_empty" => true])));
    }

    public function postTypes()
    {
        $this->verifyNonce();

        $postTypes = get_post_types([], 'objects');

        $postTypes = array_filter($postTypes, function ($postType) {
            if (function_exists('pll_is_translated_post_type')) {
                return pll_is_translated_post_type($postType->name);
            }
            return true;
        });

        $postTypes = array_filter($postTypes, function ($postType) {
            return !in_array($postType->name, ["shop_order", "wp_block"]);
        });

        $postTypes = array_values($postTypes);

        $postTypes = array_map(function ($postType) {
            return ["id" => $postType->name, "name" => $postType->labels->singular_name];
        }, $postTypes);

        $this->response($postTypes);
    }
}
