<?php

namespace Bluewing\Enumerations\EditorContent;

enum EditorContentValidationFailure
{
    case InvalidStructure;
    case InvalidMark;
    case UnknownProperty;
    case ExceedsLimit;
}
