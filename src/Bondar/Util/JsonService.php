<?php

namespace Bondar\Util;

class JsonService
{
    /**
     * @param $object
     * @param int $flags
     * @param int $depth
     * @return false|string
     */
    public static function _json_encode($object, int $flags = 0, int $depth = 512)
    {
        return \json_encode($object, $flags, $depth);
    }
}