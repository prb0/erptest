<?php

namespace Bondar\AccessLogParser;

use Bondar\Util;

class Parser
{
    /**
     * @var string
     */
    private $pathToLog;
    /**
     * @var Util\PregService
     */
    private $_pregService;

    /**
     * @var int
     */
    private $traffic = 0;
    /**
     * @var int
     */
    private $viewCount = 0;
    /**
     * @var array
     */
    private $uniqueUrls = [];
    /**
     * @var array
     */
    private $crawlers = [];
    /**
     * @var array
     */
    private $statusCodes = [];
    /**
     * @var string[]
     */
    private $interestedCrawlers = [
        'Google',
        'Yandex',
        'Bing',
        'Baidu',
    ];
    /**
     * @var string[]
     */
    private $excludedTrafficCodes = [
        '301',
    ];

    /**
     * @throws Exception
     */
    public function __construct(string $pathToLog)
    {
        if (!Util\FileService::_file_exists($pathToLog)) {
            throw new Exception('Invalid path to logfile');
        }

        $this->_pregService = new Util\PregService();
        $this->pathToLog    = $pathToLog;

        foreach ($this->interestedCrawlers as $crawler) {
            $this->crawlers[$crawler] = 0;
        }
    }

    /**
     * @return array
     * @throws Exception
     * @throws Util\Exception
     */
    public function parseData(): array
    {
        Util\Logger::info('Start new job');

        $handle = Util\FileService::_fopen($this->pathToLog, "r");

        if ($handle === false) {
            throw new Exception('File read problem');
        }

        while (($query = Util\FileService::_fgets($handle)) !== false) {
            $this->parseLine($query);
        }

        if (Util\FileService::_feof($handle) === false) {
            throw new Exception('_fgets() crushed');
        }

        Util\FileService::_fclose($handle);

        $result = [
            'urls'        => count($this->uniqueUrls),
            'views'       => $this->viewCount,
            'traffic'     => $this->traffic,
            'crawlers'    => $this->crawlers,
            'statusCodes' => $this->statusCodes,
        ];

        Util\Logger::info(print_r($result, true));
        Util\Logger::info('Complete job');

        return $result;
    }

    /**
     * @param string $line
     * @throws Util\Exception
     */
    private function parseLine(string $line): void
    {
        $query = explode('"', $line);

        $this->parseUniqueUrl($query[1]);
        $this->parseTraffic($query[2]);
        $this->parseStatusCodes($query[2]);
        $this->parseBots($query[5]);
        $this->viewCount++;
    }

    /**
     * @param string $httpFragment
     */
    private function parseUniqueUrl(string $httpFragment): void
    {
        $url = explode(' ', $httpFragment);

        if (!array_key_exists($url[1], $this->uniqueUrls)) {
            $this->uniqueUrls[$url[1]] = null;
        }
    }

    /**
     * @param string $userAgent
     * @throws Util\Exception
     */
    private function parseBots(string $userAgent): void
    {
        if ($this->isBot($userAgent)) {
            $botName = $this->_pregService->matchedValue();

            $this->crawlers[$botName]++;
        }
    }

    /**
     * @param string $mixed
     */
    private function parseStatusCodes(string $mixed): void
    {
        $url = explode(' ', $mixed);

        if (array_key_exists($url[1], $this->statusCodes)) {
            $this->statusCodes[$url[1]]++;
        } else {
            $this->statusCodes[$url[1]] = 1;
        }
    }

    /**
     * @param string $mixed
     */
    private function parseTraffic(string $mixed): void
    {
        $url = explode(' ', $mixed);

        if (!$this->isExcludedTraffic($url[1])) {
            $this->traffic += (int) $url[2];
        }
    }

    /**
     * @param string $code
     * @return bool
     */
    private function isExcludedTraffic(string $code): bool
    {
        return in_array($code, $this->excludedTrafficCodes);
    }

    /**
     * @param string $userAgent
     * @return bool
     */
    private function isBot(string $userAgent): bool
    {
        $pattern = '/(' . implode('|', $this->interestedCrawlers) .')/';
        $this->_pregService->match($pattern, $userAgent);

        return $this->_pregService->isMatched();
    }
}
