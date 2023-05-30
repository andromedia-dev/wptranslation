<?php

namespace Polytranslate\Utils;

abstract class Detector
{
    static public function polylangIsActive()
    {
        global $polylang;

        return !empty($polylang);
    }
}
