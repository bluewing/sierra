<?php

namespace Bluewing\Testing;

use Illuminate\Support\Arr;

trait InteractsWithDatabaseJson
{
    /**
     * Helper function which creates a flattened array adhering to PostgreSQL's JSON path syntax to assert that a
     * particular JSON field contains the correct data.
     *
     * @param string $table - The table being tested.
     * @param array $data - The data being asserted.
     *
     * @return $this - Returns static for fluent chaining.
     */
    protected function assertDatabaseJson(string $table, array $data): static
    {
        $flattenedData = Arr::dot($data);

        return $this->assertDatabaseHas($table, array_combine(
                array_map(fn($k) => str_replace('.', '->', $k), array_keys($flattenedData)),
                array_values($flattenedData))
        );
    }
}
