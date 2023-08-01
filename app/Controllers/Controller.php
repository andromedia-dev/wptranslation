<?php

namespace WPTranslation\Controllers;

use WPTranslation\Utils\Validator;
use WPTranslation\Vendors\Illuminate\Validation\ValidationException;

class Controller
{
    /**
     * data passed by post
     *
     * @var array
     */
    protected $request = [];

    public function __construct()
    {
        if (!empty($_POST["payload"])) {
            $this->request = json_decode(stripcslashes($_POST["payload"]), true);
        }
    }

    protected function response($data = [], $status = 200)
    {
        wp_send_json($data, $status);
    }

    protected function emptyResponse($status = 200)
    {
        wp_die('', '', [
            'code' => $status,
        ]);
    }

    protected function verifyNonce($key = "_ajax_nonce")
    {
        if (!isset($_POST[$key]) || !wp_verify_nonce($_POST[$key], WPTRANSLATION_NONCE)) {
            $this->response(["message" => "Page expired.", "error" => "Refresh the page and retry."], 403);
        }
    }

    protected function verifyPremium()
    {
        if (!wptranslation_fs()->can_use_premium_code()) {
            $this->response(["message" => "Invalid license.", "error" => "You must have a valid license to use this plugin."], 403);
        }
    }

    protected function validate($rules, $messages = [], $customAttributes = [])
    {
        $validator = new Validator($this->request, $rules, $messages, $customAttributes);
        try {
            return $validator->validate();
        } catch (ValidationException $e) {
            $this->response(["message" => $e->getMessage(), "errors" => $e->validator->getMessageBag()], 422);
        }
    }
}
