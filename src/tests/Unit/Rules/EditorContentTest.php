<?php

namespace Tests\Unit\Rules;

use Bluewing\Concerns\Faker\EditorContentSchemaProvider;
use Bluewing\Rules\EditorContent;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase;

class EditorContentTest extends TestCase
{
    use WithFaker;

    /**
     * Configures faker to use our created `EditorContentSchemaProvider` class.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->faker->addProvider(new EditorContentSchemaProvider($this->faker));
    }

    /**
     * `EditorContent`'s `passes` method should always return `true` if the structure of the content provided to the
     * field is valid.
     *
     * @return void
     */
    public function test_passes_for_valid_structure(): void
    {
        $this->assertTrue((new EditorContent)->passes('field', $this->faker->editorContent()));
    }

    /**
     * An empty paragraph node, with no `content` key is allowed.
     *
     * @return void
     */
    public function test_empty_paragraph_is_passable(): void
    {
        $this->assertTrue((new EditorContent)->passes('field', [
            "type"      => "doc",
            "content"   => [
                [
                    "type" => "paragraph"
                ],
            ]
        ]));
    }

    /**
     * If the root node of the structure is not of type 'doc', or an invalid intermediate node type is found, or a
     * mandatory property does not exist at the node, then the `EditorContent` rule should fail indicating the property
     * has invalid structure.
     *
     * @return void
     */
    public function test_fails_with_invalid_structure_when_editor_content_schema_is_incorrect(): void
    {
        $scenarios = [
            [
                'type' => 'foo',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [['type' => 'text', 'text' => 'foo']]
                    ]
                ]
            ],
            [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'notValid',
                        'content' => [['type' => 'text', 'text' => 'foo']]
                    ]
                ]
            ]
        ];

        foreach ($scenarios as $scenario) {
            $editorContent = new EditorContent;
            $this->assertFalse($editorContent->passes('field', $scenario));
            $this->assertTrue($editorContent->message() === 'The content for :attribute is not valid.');
        }
    }

    /**
     * If an invalid mark type is found for a text node, then `EditorContent` should fail the validation.
     *
     * @return void
     */
    public function test_fails_with_invalid_mark_if_disallowed_mark_type_is_found(): void
    {
        $editorContent   = new EditorContent;
        $invalidMarkType = 'invalid mark';

        $this->assertFalse($editorContent->passes('field', [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'valid text',
                            'marks' => [['type' => $invalidMarkType]]
                        ]
                    ]
                ]
            ]
        ]));
        $this->assertTrue($editorContent->message() === "The mark '$invalidMarkType' for :attribute is not allowed.");
    }

    /**
     * If a property is found on a node that is not explicitly on the allowed list of properties for a node,
     * then the `EditorContent` rule should fail indicating an unknown property has been found.
     *
     * @return void
     */
    public function test_fails_with_unknown_property_where_disallowed_property_is_found_at_node(): void
    {
        $editorContent      = new EditorContent;
        $invalidProperty    = 'invalid prop';

        $this->assertFalse($editorContent->passes('field', [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    $invalidProperty => 'foo',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'valid text',
                            'marks' => [['type' => 'bold']]
                        ]
                    ]
                ]
            ]
        ]));
        $this->assertTrue($editorContent->message() === "The property '$invalidProperty' for :attribute is not allowed.");
    }

    /**
     * If the length of the text content of the `EditorContent` exceeds the predefined maximum, the validation should
     * fail indicating the length of the content exceeds the allowed limit.
     *
     * @return void
     */
    public function test_fails_with_exceeds_limit_if_text_content_exceeds_character_limit(): void
    {
        $maxLength      = 100;
        $editorContent  = new EditorContent($maxLength);
        $textStrings    = [];

        for ($i = 0; $i < 3; $i++) {
            $textStrings[] = [
                'type' => 'text',
                'text' => $this->faker->asciify(str_repeat('*', 20))
            ];
        }

        $this->assertFalse($editorContent->passes('field', [
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => $textStrings],
                ['type' => 'paragraph', 'content' => $textStrings]
            ]
        ]));
        $this->assertTrue($editorContent->message() === "The length of :attribute should be shorter than $maxLength characters.");
    }
}
