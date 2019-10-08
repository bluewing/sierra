<?php 

namespace Bluewing\Services;

use Exception;

class TokenGenerator {

    /**
     * Generates a cryptographically secure token of `ofLength` and returns it. Optionally, prepend a prefix to the
     * token as needed.
     *
     * @param int $ofLength - The length of the string to generate.
     * @param string|null $prefix - A string the token should be prefixed with.
     * @param bool $trimToLength - If a token has a prefix attached, setting this to true will yield a token that adheres
     * to $ofLength, and part of the unique characters of the token will be truncated.
     *
     * @return string - The requested token returned to the callee.
     *
     * @throws Exception - If the random_bytes could not be generated.
     */
    public function generate(int $ofLength, string $prefix = null, bool $trimToLength = true): string
    {
        if ($prefix !== null && $trimToLength) {
            $ofLength -= strlen($prefix) + 1;
        }

        $bytes = random_bytes($ofLength / 2);

        if ($prefix === null) {
            return bin2hex($bytes);
        }

        return $prefix . '_' . bin2hex($bytes);
    }
}
