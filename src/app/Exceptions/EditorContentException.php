<?php

namespace Bluewing\Exceptions;

use Bluewing\Enumerations\EditorContent\EditorContentValidationFailure;
use Exception;

class EditorContentException extends Exception
{
    /**
     * @param EditorContentValidationFailure $failureReason - The failure reason that resulted in the exception being
     * thrown.
     * @param string $message - The associated contents with the failure.
     */
    public function __construct(public EditorContentValidationFailure $failureReason, string $message = "")
    {
        parent::__construct($message);
    }
}
