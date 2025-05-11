<?php
// app/Helpers/StrictValidator.php

namespace App\Helpers;

use App\Exceptions\UnexpectedFieldException;

class StrictValidator
{
    public static function ensureOnlyAllowed(array $data, array $allowedKeys): void
    {
        $extraKeys = array_diff(array_keys($data), $allowedKeys);
        if (!empty($extraKeys)) {
            throw new UnexpectedFieldException($extraKeys);
        }
    }
}
