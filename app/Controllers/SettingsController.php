<?php

namespace Polytranslate\Controllers;

use Exception;
use Polytranslate\Controllers\Controller;
use Polytranslate\Utils\Settings;
use Polytranslate\Vendors\DeepL\AuthorizationException;
use Polytranslate\Vendors\DeepL\Translator as DeepLTranslator;
use Polytranslate\Vendors\Illuminate\Support\Arr;
use Polytranslate\Vendors\Illuminate\Validation\Rule;
use Polytranslate\Vendors\Orhanerday\OpenAi\OpenAi;

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
