<?php

use Bondar\AccessLogParser\Parser;
use Bondar\Util;

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/config.php';

header('Content-type: application/json');

try {
    if (empty($_REQUEST['path'])) {
        throw new \Exception('Filename arg missed ($_REQUEST[path])');
    }

    $path   = $_REQUEST['path'];
    $parser = new Parser($path);
    $result = $parser->parsedData();

    echo Util\JsonService::_json_encode($result);
} catch (\Exception $e) {
    Util\Logger::exception($e);

    if ($e instanceof Util\HumanReadableInterface) {
        echo $e;
    }
}
