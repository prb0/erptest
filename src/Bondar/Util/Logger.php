<?php

namespace Bondar\Util;

class Logger
{
    const LINE_DELIMITER  = PHP_EOL;
    const BLOCK_DELIMITER = '===============================================' . PHP_EOL;

    /**
     * @param \Throwable $e
     * @throws Exception
     */
    public static function exception(\Throwable $e): void
    {
        $message = 'Message: ' . $e->getMessage()       . static::LINE_DELIMITER;
        $message.= 'Trace: '   . $e->getTraceAsString() . static::LINE_DELIMITER;

        static::log($message, LOG_DIR_EXCEPTION);
    }

    /**
     * @param string $message
     * @throws Exception
     */
    public static function info(string $message): void
    {
        static::log($message . static::LINE_DELIMITER, LOG_DIR_INFO);
    }

    /**
     * @param string $message
     * @param string $logDir
     * @throws Exception
     */
    private static function log(string $message, string $logDir): void
    {
        try {
            $dateTime    = DateService::instance();
            $logFileName = $dateTime->format('Y_m_d');
            $time        = $dateTime->format('H:i:s');
        } catch (\Exception $e) {
            $logFileName = 'undefined';
            $message .= 'DateService trouble: '                . static::LINE_DELIMITER;
            $message .= 'Message: ' . $e->getMessage()         . static::LINE_DELIMITER;
            $message .= 'Trace: '   . $e->getTraceAsString()   . static::LINE_DELIMITER;
        }

        if (!empty($time)) {
            $message = $time . static::LINE_DELIMITER . $message;
        }

        $message.= static::BLOCK_DELIMITER;

        if (!FileService::_is_dir($logDir)) {
            FileService::_mkdir($logDir, 0774, true);
        }

        $status = FileService::_file_put_contents($logDir . $logFileName, $message);

        if ($status === false) {
            throw new Exception('File write error');
        }
    }
}
