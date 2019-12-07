<?php

namespace Bluewing\Schema;

use ReflectionClass;
use ReflectionException;

/**
 * Trait ManagesModelComments
 *
 * @package Bluewing\Schema;
 *
 * @see https://stackoverflow.com/questions/22444685/extend-blueprint-class/57539154#57539154
 */
trait ManagesModelComments
{
    /**
     * Adds the property PhpDoc comments from the Model class to the database column definition.
     *
     * @param string $class - The class that should be instantiated to reflect
     * and retrieve PHPDocumentation from to extract property comments.
     *
     * @throws NoModelCommentException - If the Model does not have any comments present.
     * @throws ReflectionException - If a `ReflectionClass` could not be instantiated from the provided string.
     */
    public function addModelComments(string $class): void
    {
        $reflection = new ReflectionClass($class);
        $parsedPropertyComments = $this->parsePropertyDocTagsFromModel($reflection);

        foreach ($this->getColumns() as $columnDefinition) {
            $columnDefinition->comment($parsedPropertyComments[$columnDefinition->name]);
        }
    }

    /**
     * Retrieves the DocComment, parses it with regex, and remaps the result into an array of key values,
     * representing the name of the column and the description that should become the comment.
     *
     * @param ReflectionClass $reflectionClass - The class to extract a PhpDocComment from, and parse out
     * `@property` tags.
     *
     * @return array - An array of key value pairs representing the name of the column, and the comment that
     * column should contain.
     *
     * @throws NoModelCommentException - If the Model does not have any comments present.
     */
    private function parsePropertyDocTagsFromModel(ReflectionClass $reflectionClass): array
    {
        $modelComment = $reflectionClass->getDocComment();

        if ($modelComment === null) {
            throw new NoModelCommentException();
        }

        preg_match_all(
            '/@property\s(?:[A-Za-z]*?)\s\$?([[:alnum:]]*?)\s-\s((?:(?!@property).)*)/s',
            $modelComment,
            $matches,
            PREG_SET_ORDER
        );

        return array_combine(array_map(function($match) {
            return $match[1];
        }, $matches), array_map(function($match) {
            return $match[2];
        }, $matches));
    }
}
