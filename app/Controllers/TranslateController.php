<?php

namespace WPTranslation\Controllers;

use WPTranslation\Utils\Detector;
use WPTranslation\Vendors\Illuminate\Pagination\LengthAwarePaginator;
use WPTranslation\Vendors\Illuminate\Support\Arr;
use WP_Query;

class TranslateController extends Controller
{
    private $perPage = 25;

    public function index()
    {
        $this->verifyNonce();

        $this->validate([
            "page" => ["nullable", "numeric"],
            "post_type" => ["nullable", "string"],
            "category" => ["nullable", "numeric"],
            "author" => ["nullable", "numeric"],
            "search" => ["nullable", "string"],
            "sort_order" => ["nullable", "string"],
            "sort_direction" => ["nullable", "string"],
        ]);

        if (!Detector::polylangIsActive()) {
            $this->response(["message" => "Polylang not detected.", "error" => "Polylang is missing. Activate it to use WPTranslation."], 503);
        }

        // get default language
        $language = pll_default_language();
        $languages = pll_the_languages(['raw' => 1]);

        $page = Arr::get($this->request, "page", 1);

        // get posts link to default language
        $args = [
            'lang' => $language,
            'posts_per_page' => $this->perPage,
            'paged' => $page,
            'tax_query' => [
                [
                    'taxonomy' => 'language',
                    'field' => 'term_id',
                    'terms' => $languages[$language]["id"]
                ]
            ]
        ];

        // make query
        $query = $this->makeQuery($args, $this->request);

        // create output
        $posts = [];
        foreach ($query->posts as $post) {
            $posts[] = [
                "id" => $post->ID,
                "post_author" => [
                    'id' => $post->post_author,
                    'name' => get_the_author_meta('display_name', $post->post_author),
                ],
                "post_date" => $post->post_date,
                "post_title" => $post->post_title,
                "post_status" => $post->post_status,
                "categories" => wp_get_post_categories($post->ID, ['fields' => 'names']),
                "tags" => wp_get_post_tags($post->ID, ['fields' => 'names']),
                "language" => pll_get_post_language($post->ID),
                "translations" => pll_get_post_translations($post->ID),
            ];
        }

        // pagination
        $paginator = new LengthAwarePaginator($posts, $query->found_posts, $this->perPage, $page);

        $this->response($paginator->toArray());
    }

    private function makeQuery($args, $filters = [])
    {
        $args["post_status"] = "publish";

        // filters
        $category = Arr::get($filters, "category", null);
        $productCategory = Arr::get($filters, "product_category", null);
        if (!empty($category) || !empty($productCategory)) {
            $args["tax_query"] = [];
            if (!empty($category)) {
                $args["tax_query"][] = [
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $category,
                ];
            }
            if (!empty($productCategory)) {
                $args["tax_query"][] = [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $productCategory,
                ];
            }
        }

        $author = Arr::get($filters, "author", null);
        if (!empty($author)) {
            $args["author"] = $author;
        }
        $postType = Arr::get($filters, "post_type", []);
        if (!empty($postType)) {
            $args["post_type"] = $postType;
        } else {
            $args["post_type"] = ['post', 'page'];
        }

        $search = Arr::get($filters, "search", null);
        if (!empty($search)) {
            $args["s"] = $search;
        }

        // order
        $sortOrder = Arr::get($filters, "sort_order", null);
        $sortDirection = Arr::get($filters, "sort_direction", "desc");
        if (!empty($sortOrder)) {
            switch ($sortOrder) {
                case "title";
                    $args["orderby"] = "title";
                    break;
                case "date";
                    $args["orderby"] = "date";
                    break;
            }
            $args["order"] = $sortDirection;
        }

        return new WP_Query($args);
    }
}
