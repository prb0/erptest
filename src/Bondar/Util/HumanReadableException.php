<?php

namespace Bondar\Util;

class HumanReadableException extends \Exception implements HumanReadableInterface
{
    public function __toString(): string
    {
        return JsonService::_json_encode([
            'success' => false,
            'message' => $this->getMessage(),
        ]);
    }
}