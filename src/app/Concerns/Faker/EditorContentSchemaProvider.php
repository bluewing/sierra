<?php

namespace Bluewing\Concerns\Faker;

use Bluewing\Enumerations\EditorContent\EditorContentMarkType;
use Exception;
use Faker\Provider\Base as FakerProviderBase;

class EditorContentSchemaProvider extends FakerProviderBase
{
    /**
     * Supplies a `fakerphp` provider that can generate realistic JSON content that would be created by the tiptap
     * editor in the client.
     *
     * @return array - The created editor content as a deeply-nested associative array.
     *
     * @throws Exception - An `Exception` is thrown by `generateParagraphNodes` if a cryptographically secure source of
     * entropy cannot be located.
     */
    public function editorContent(): array
    {
        return [
            'type' => 'doc',
            'content' => $this->generateParagraphNodes($this->generator->biasedNumberBetween(1, 3))
        ];
    }

    /**
     * Generates one or more realistic paragraph nodes that would be created by the tiptap editor in the client. Each
     * paragraph node consists of a `type` key, along with a `content` array containing child text nodes. If the
     * paragraph node is empty, the `content` key may not be included.
     *
     * @param int $count - The number of paragraph nodes to generate. Defaults to 1. Must be a positive integer.
     *
     * @return array - An `array` of paragraph nodes that can be affixed to the root editor content object.
     *
     * @throws Exception - An `Exception` is thrown by `random_int` if a cryptographically secure source of entropy
     * cannot be located.
     */
    private function generateParagraphNodes(int $count = 1): array
    {
        $paragraphNodes = [];
        for (; $count > 0; $count--) {
            $node = ['type' => 'paragraph'];

            if ((bool) random_int(0, 1)) {
                $node['content'] = $this->generateTextNodes($this->generator->biasedNumberBetween(1, 3));
            }

            $paragraphNodes[] = $node;
        }
        return $paragraphNodes;
    }

    /**
     * Generates one or more realistic text nodes that would be created by the tiptap editor in the client. Each text
     * node consists of a `type` key, along with a `text` key containing the contents of the node. Additionally, if
     * formatting has been applied to the node, `marks` will be present indicating as such.
     *
     * @param int $count - The number of text nodes to generate. Defaults to 1. Must be a positive integer.
     *
     * @return array - An `array` of text nodes that can be affixed to the parent paragraph node.
     */
    private function generateTextNodes(int $count = 1): array
    {
        $textNodes = [];
        for (; $count > 0; $count--) {
            $textNode = [
                'type' => 'text',
                'text' => $this->generator->sentence
            ];

            if ($this->generator->boolean) {
                $textNode['marks'] = $this->generateMarks($this->generator->biasedNumberBetween(1, 3));
            }

            $textNodes[] = $textNode;
        }
        return $textNodes;
    }

    /**
     * Generates one or more marks that would be created by the tiptap editor in the client. Each mark consists of a
     * type key, with a value that matches one of the cases in the `EditorContentMarkType` enumeration.
     *
     * @param int $count - The number of marks to generate. Defaults to 1. Must be a positive integer.
     *
     * @return array - An `array` of marks that can be affixed to the parent text node.
     */
    private function generateMarks(int $count = 1): array
    {
        $marks = [];
        for (; $count > 0; $count--) {
            $marks[] = ['type' => $this->generator->randomElement(EditorContentMarkType::asBackedValueArray())];
        }
        return $marks;
    }
}
