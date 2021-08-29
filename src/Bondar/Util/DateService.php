<?php

namespace Bondar\Util;

use DateTime;
use DateTimeZone;

class DateService
{
    /**
     * @param string $dateTime
     * @param DateTimeZone|null $timezone
     * @return DateTime|null
     * @throws Exception
     */
    public static function instance(string $dateTime = 'now', ?DateTimeZone $timezone = null): ?DateTime
    {
        try {
            return new DateTime($dateTime, $timezone);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
