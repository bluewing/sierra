<?php

namespace Bluewing\Rules;

use Bluewing\Enumerations\EditorContent\EditorContentMarkType;
use Bluewing\Enumerations\EditorContent\EditorContentNodeType;
use Bluewing\Enumerations\EditorContent\EditorContentValidationFailure;
use Bluewing\Exceptions\EditorContentException;
use Illuminate\Contracts\Validation\Rule;

/**
 * @method array getNodeContent(array $node)
 * @method string getNodeType(array $node)
 * @method array getNodeMarks(array $node)
 * @method string getNodeText(array $node)
 */
class EditorContent implements Rule
{
    /**
     * @var int - The accumulated character length of the contents in the editor. If this exceeds a pre-determined
     * maximum character length, then the rule will fail.
     */
    private int $accumulatedCharacterLength = 0;

    /**
     * @var EditorContentException|null - The generated `EditorContentException` that will be thrown if the
     * `EditorContent` is invalid.
     */
    private ?EditorContentException $generatedException = null;

    /**
     * Constructs the `EditorContent` rule with an optional maximum character length that can also be used to enforce
     * a maximum length of the editor contents.
     *
     * @param int|null $maximumCharacterLength - The maximum character length of the editor contents. Defaults to
     * `null` if no character length validation is required.
     */
    public function __construct(private ?int $maximumCharacterLength = null) {}

    /**
     * Validates the `JSONContent` typescript schema that is created via use of the `tiptap` rich text editor. This is
     * performed recursively by repeatedly instantiating `EditorContent` rules for each level of the content being
     * sanitized.
     *
     * Any `mark`'s or `type`'s which do not explicitly match the allow list of provided values will immediately
     * terminate validation of the `JSONContent` and fail.
     *
     * Additionally, any unknown properties will also cause the validation to fail.
     *
     * @param string $attribute - The name of the attribute being validated.
     * @param mixed $value - The JSONContent of the attribute.
     *
     * @return bool - `true` if the rule passes, `false` otherwise.
     */
    public function passes($attribute, $value)
    {
        try {
            $this->validate($value);
            return true;
        } catch (EditorContentException $e) {
            $this->generatedException = $e;
            return false;
        }
    }

    /**
     * Validates the structure, length, and content of the value passed to the `EditorContent` instance. If any
     * invalid situation is found, then an `EditorContentException` will be thrown which will terminate execution and
     * cause the validator to fail.
     *
     * @param array $nodeToValidate - The node to be validated for the content.
     * @param int $level - The level of the content being validated.
     *
     * @throws EditorContentException - An `EditorContentException` will be thrown if the `EditorContent` is invalid.
     */
    private function validate(array $nodeToValidate, int $level = 0): void
    {
        // Validates the node itself.
        $this->{'validate' . ($level === 0 ? 'Root' : 'Intermediate') . 'Node'}($nodeToValidate);

        // Checks the node does not have any unknown properties.
        $this->checkForUnknownProperties($nodeToValidate);

        // Validate child nodes, if they exist.
        if ($this->canNodeHaveChildren($nodeToValidate)) {
            foreach ($this->getNodeContent($nodeToValidate) ?? [] as $childNode) {
                $this->validate($childNode, $level + 1);
            }
        }
    }

    /**
     * The root node of an `EditorContent` value should have a `type` of "doc". If this is not true, an
     * `EditorContentException` is thrown.
     *
     * @param array $node - The node being validated.
     *
     * @throws EditorContentException - Thrown if the root node does not have a `type` of "doc".
     */
    private function validateRootNode(array $node): void
    {
        if ($this->getNodeType($node) !== EditorContentNodeType::Doc->value) {
            throw new EditorContentException(EditorContentValidationFailure::InvalidStructure);
        }
    }

    /**
     * The intermediate node of an `EditorContent` should have a `type` of either "paragraph" or "text". If this is not
     * true, `EditorContentException` is thrown.
     *
     * @param array $node - The node being validated.
     *
     * @throws EditorContentException - Thrown if the intermediate node does not have a `type` of either "paragraph"
     * or "text".
     */
    private function validateIntermediateNode(array $node): void
    {
        match (EditorContentNodeType::tryFrom($this->getNodeType($node))) {
            EditorContentNodeType::Paragraph    => null,
            EditorContentNodeType::Text         => $this->validateTextNode($node),
            default                             => throw new EditorContentException(EditorContentValidationFailure::InvalidStructure)
        };
    }

    /**
     * Validates the `text` node by ensuring it has the appropriate `marks`, and additionally appends the length of
     * the `text` field to the `accumulatedCharacterLength` property.
     *
     * @param array $node - The node being validated.
     *
     * @throws EditorContentException - The use of `validateMarks` will throw this exception.
     */
    private function validateTextNode(array $node): void
    {
        $this->validateMarks($node);
        $this->accumulatedCharacterLength += strlen($this->getNodeText($node));

        if ($this->hasLimitBeenExceeded()) {
            throw new EditorContentException(EditorContentValidationFailure::ExceedsLimit);
        }
    }

    /**
     * Validates the `marks` of a node by ensuring they are an appropriate value. Only "bold", "italic", and
     * "underline" are accepted values.
     *
     * @param array $node - The node being validated.
     *
     * @throws EditorContentException - Will throw an exception if the mark type found is not in the
     * `EditorContentMarkType` enumeration.
     */
    private function validateMarks(array $node): void
    {
        try {
            $marks = $this->getNodeMarks($node);
        } catch (EditorContentException) {
            $marks = [];
        }

        foreach ($marks as $mark) {
            $mark = $this->getNodeType($mark);
            if (!in_array($mark, EditorContentMarkType::asBackedValueArray())) {
                throw new EditorContentException(EditorContentValidationFailure::InvalidMark, $mark);
            }
        }
    }

    /**
     * Boolean helper that indicates if the node can contain child nodes.
     *
     * @param array $node - The `node` being evaluated for whether it can contain child nodes.
     *
     * @return bool - `true` if the node can contain child nodes, `false` otherwise.
     *
     * @throws EditorContentException - Will be thrown if the node type does not match any of the predefined cases in
     * the `EditorContentNodeType` enumeration.
     */
    private function canNodeHaveChildren(array $node): bool
    {
        return match (EditorContentNodeType::tryFrom($this->getNodeType($node))) {
            EditorContentNodeType::Doc, EditorContentNodeType::Paragraph => true,
            EditorContentNodeType::Text => false,
            default => throw new EditorContentException(EditorContentValidationFailure::InvalidStructure)
        };
    }

    /**
     * Fetches a property with the given `key` in the provided `node`. If no `key` exists in the node, an
     * `EditorContentException` will be thrown.
     *
     * @param array $node - The node to retrieve the property from.
     * @param string $key - The key for the property.
     *
     * @returns mixed - The property retrieved from the node.
     *
     * @throws EditorContentException - Will be thrown if the `key` does not exist in the provided `node`.
     */
    private function getPropertyInNode(array $node, string $key): mixed
    {
        if (array_key_exists($key, $node)) {
            return $node[$key];

        } else if ($node['type'] === EditorContentNodeType::Paragraph->value && $key === 'content') {
            return [];
        }

        throw new EditorContentException(EditorContentValidationFailure::InvalidStructure);
    }

    /**
     * Checks that the accumulated character length of the attribute does not exceed the predetermined maximum length.
     * If no `maximumCharacterLength` has been provided, it defaults to `null`, and this method will return `true`.
     *
     * @return bool - `true` if the character limit has been exceeded or no character limit is defined, `false`
     * otherwise.
     */
    private function hasLimitBeenExceeded(): bool
    {
        return $this->maximumCharacterLength !== null &&
            $this->accumulatedCharacterLength > $this->maximumCharacterLength;
    }

    /**
     * Checks the provided `node` only contains the allowed properties as defined in the `allowedChildProperties`
     * method.
     *
     * @param array $node - The node being validated.
     *
     * @throws EditorContentException - An `EditorContentException` will be thrown if an unknown property is discovered
     * on the provided `node`.
     */
    private function checkForUnknownProperties(array $node): void
    {
        foreach (array_keys($node) as $keyInNode) {
            if (!in_array($keyInNode, $this->allowedChildProperties(EditorContentNodeType::tryFrom($this->getNodeType($node))))) {
                throw new EditorContentException(EditorContentValidationFailure::UnknownProperty, $keyInNode);
            }
        }
    }

    /**
     * Fetches an array of strings representing the allowed child properties that can be present in each node. This
     * function can be used to determine if a node fails validation by containing and unknown property.
     *
     * @param EditorContentNodeType $type - The type of the node being checked.
     *
     * @return string[] - Defines an `array` of the allowed child properties that can contained in each node.
     *
     * @throws EditorContentException - If the node type provided does not match any of the `EditorContentNodeType`'s.
     */
    private function allowedChildProperties(EditorContentNodeType $type): array
    {
        return match ($type) {
            EditorContentNodeType::Doc, EditorContentNodeType::Paragraph  => ['type', 'content'],
            EditorContentNodeType::Text => ['type', 'marks', 'text'],
            default => throw new EditorContentException(EditorContentValidationFailure::InvalidStructure)
        };
    }

    /**
     * Get the validation error message based on the specific validation failure recorded in the
     * `EditorContentException`.
     *
     * @return string - The validation error message.
     */
    public function message()
    {
        return match ($this->generatedException?->failureReason) {
            EditorContentValidationFailure::InvalidStructure    => 'The content for :attribute is not valid.',
            EditorContentValidationFailure::InvalidMark         => "The mark '{$this->generatedException->getMessage()}' for :attribute is not allowed.",
            EditorContentValidationFailure::UnknownProperty     => "The property '{$this->generatedException->getMessage()}' for :attribute is not allowed.",
            EditorContentValidationFailure::ExceedsLimit        => "The length of :attribute should be shorter than {$this->maximumCharacterLength} characters.",
            default                                             => 'The content for :attribute is not valid.'
        };
    }

    /**
     * Handles fetching a property of a node by matching any method names which start with `getNode`.
     *
     * @param $name - The name of the method that was called.
     * @param $arguments - The arguments passed to the method that was called.
     *
     * @throws EditorContentException - An `EditorContentException` will be thrown by `getPropertyInNode` if the
     * `key` does not exist in the `node`.
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/getNode([A-Za-z]+)/', $name, $matches) === 1) {
            return $this->getPropertyInNode($arguments[0], lcfirst($matches[1]));
        }
    }
}
