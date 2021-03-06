<?php

namespace Bondar\Util;

use Generator;

class FileService
{
    /**
     * @param string $path
     * @return Generator
     * @throws Exception
     */
    public static function getLines(string $path): Generator
    {
        $handle = static::_fopen($path, "r");

        if ($handle === false) {
            throw new Exception('File read problem');
        }

        while (($line = static::_fgets($handle)) !== false) {
            yield $line;
        }

        if (static::_feof($handle) === false) {
            throw new Exception('_fgets() crushed');
        }

        static::_fclose($handle);
    }

    /**
     * @param string $directory
     * @param int $permissions
     * @param bool $recursive
     * @return bool
     */
    public static function _mkdir(string $directory, int $permissions = 0444, bool $recursive = false): bool
    {
        return \mkdir($directory, $permissions, $recursive);
    }

    /**
     * @param string $path
     * @param string $mode
     * @return false|resource
     */
    public static function _fopen(string $path, string $mode)
    {
        return \fopen($path, $mode);
    }

    /**
     * @param $handle
     * @param int $bufferSize
     * @return false|string
     */
    public static function _fgets($handle, int $bufferSize = 4096)
    {
        return \fgets($handle, $bufferSize);
    }

    /**
     * @param string $path
     * @param string $data
     * @param int $flag
     * @return false|int
     */
    public static function _file_put_contents(string $path, string $data, int $flag = FILE_APPEND)
    {
        return file_put_contents($path, $data, $flag);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public static function _is_dir(string $filename): bool
    {
        return \is_dir($filename);
    }

    /**
     * @param $handle
     * @return bool
     */
    public static function _feof($handle): bool
    {
        return \feof($handle);
    }

    /**
     * @param $handle
     * @return bool
     */
    public static function _fclose($handle): bool
    {
        return \fclose($handle);
    }

    /**
     * @param string $path
     * @return bool
     */
    public static function _file_exists(string $path): bool
    {
        return file_exists($path);
    }
}
