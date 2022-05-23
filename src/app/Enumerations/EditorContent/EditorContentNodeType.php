<?php

namespace Bluewing\Enumerations\EditorContent;

enum EditorContentNodeType: string
{
    case Doc = 'doc';
    case Text = 'text';
    case Paragraph = 'paragraph';
}
