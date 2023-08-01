<?php

namespace WPTranslation\Controllers;

use Exception;
use WPTranslation\Controllers\Controller;
use WPTranslation\Utils\Settings;
use WPTranslation\Vendors\DeepL\AuthorizationException;
use WPTranslation\Vendors\DeepL\Translator as DeepLTranslator;
use WPTranslation\Vendors\Illuminate\Support\Arr;
use WPTranslation\Vendors\Illuminate\Validation\Rule;
use WPTranslation\Vendors\Orhanerday\OpenAi\OpenAi;

class SettingsController extends Controller
{
    public function index()
    {
        $this->verifyNonce();

        $this->response(Settings::get());
    }

    public function update()
    {
        $this->verifyNonce();

        $this->validate([
            "service" => ["required", Rule::in(["google-translate-free"])],
            "translation.post_meta.enabled" => ["boolean"],
            "translation.yoast.enabled" => ["boolean"],
        ]);

        Settings::update($this->request);

        Settings::save();

        $this->emptyResponse();
    }
}
