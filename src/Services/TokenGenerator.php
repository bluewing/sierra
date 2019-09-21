<?php 

namespace Bluewing\Services;

use Exception;

class TokenGenerator {
    
    /**
     * Generates a cryptographically secure token of `ofLength` and returns it.
     *
     * @param int $ofLength - The length of thr string to generate.
     *
     * @return string - The requested token of $ofLength returned to the callee.
     *
     * @throws Exception - If the random_bytes could not be generated.
     */
    public function generate(int $ofLength): string {
        $bytes = random_bytes($ofLength / 2);
        return bin2hex($bytes);
    }
}