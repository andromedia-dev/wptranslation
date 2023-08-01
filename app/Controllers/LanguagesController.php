<?php

namespace WPTranslation\Controllers;

class LanguagesController extends Controller
{
    public function __invoke()
    {
        $this->verifyNonce();

        $this->response([
            "default" => function_exists('pll_default_language') ? pll_default_language() : 'en',
            "all" => function_exists('pll_the_languages') ? pll_the_languages([
                'hide_if_empty' => 0,
                'raw' => 1
            ]) : [],
        ]);
    }
}
