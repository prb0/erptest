<?php

namespace Bondar\Util;

class PregService
{
    /**
     * @var int
     */
    private $numMatches = 0;

    /**
     * @var array
     */
    private $matches = [];

    /**
     * @param string $pattern
     * @param string $haystack
     * @param int $flag
     */
    public function match(string $pattern, string $haystack, int $flag = PREG_OFFSET_CAPTURE)
    {
        $this->numMatches = preg_match($pattern, $haystack, $this->matches, $flag);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function matchedValue(): string
    {
        if (empty($this->matches[0][0]) || !is_string($this->matches[0][0])) {
            throw new Exception('Ошибка разбора строки');
        }
        return $this->matches[0][0];
    }

    /**
     * @return bool
     */
    public function isMatched(): bool
    {
        return $this->numMatches > 0;
    }
}
