<?php
// app/Exceptions/UnexpectedFieldException.php

namespace App\Exceptions;

use Exception;

class UnexpectedFieldException extends Exception
{
    protected array $extraFields;

    public function __construct(array $extraFields)
    {
        

        parent::__construct("Field tidak dikenali: " . implode(', ', $extraFields));
        $this->extraFields = $extraFields;
    }

    public function getExtraFields(): array
    {
        return $this->extraFields;
    }
}
