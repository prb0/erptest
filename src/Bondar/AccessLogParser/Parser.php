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
    private $viewsCount = 0;
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
     * @param string $pathToLog
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
    public function parsedData(): array
    {
        Util\Logger::info('Start new job');

        foreach(Util\FileService::getLines($this->pathToLog) as $line) {
            $this->parseLine($line);
        }

        Util\Logger::info('Complete job');

        $result = [
            'urls'        => count($this->uniqueUrls),
            'views'       => $this->viewsCount,
            'traffic'     => $this->traffic,
            'crawlers'    => $this->crawlers,
            'statusCodes' => $this->statusCodes,
        ];

        Util\Logger::info(print_r($result, true));

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
        $this->viewsCount++;
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
        $mixed = explode(' ', $mixed);

        if (array_key_exists($mixed[1], $this->statusCodes)) {
            $this->statusCodes[$mixed[1]]++;
        } else {
            $this->statusCodes[$mixed[1]] = 1;
        }
    }

    /**
     * @param string $mixed
     */
    private function parseTraffic(string $mixed): void
    {
        $mixed = explode(' ', $mixed);

        if (!$this->isExcludedTraffic($mixed[1])) {
            $this->traffic += (int) $mixed[2];
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
