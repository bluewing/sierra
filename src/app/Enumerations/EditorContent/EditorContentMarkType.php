<?php

namespace Bluewing\Enumerations\EditorContent;

use Bluewing\Enumerations\MapsToArrays;

enum EditorContentMarkType: string
{
    use MapsToArrays;

    case Bold = 'bold';
    case Italic = 'italic';
    case Underline = 'underline';
}
