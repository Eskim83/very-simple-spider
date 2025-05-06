<?php

namespace VerySimpleSpider\Support;

class RobotsTxt
{
    protected array $disallowRules = [];

    public function __construct(string $baseUrl)
    {
        $this->load($baseUrl);
    }

    protected function load(string $baseUrl): void
    {
        $parsed = parse_url($baseUrl);
        $host = $parsed['scheme'] . '://' . $parsed['host'];
        $robotsUrl = rtrim($host, '/') . '/robots.txt';

        $content = @file_get_contents($robotsUrl);
        if (!$content) return;

        $userAgent = '*';
        $lines = explode("\n", $content);
        $active = false;

        foreach ($lines as $line) {
            $line = trim(explode('#', $line)[0]);
            if (stripos($line, 'User-agent:') === 0) {
                $ua = trim(substr($line, 11));
                $active = ($ua === '*' || strcasecmp($ua, 'VerySimpleSpider/2.0') === 0);
            } elseif ($active && stripos($line, 'Disallow:') === 0) {
                $path = trim(substr($line, 9));
                if ($path !== '') {
                    $this->disallowRules[] = $path;
                }
            }
        }
    }

    public function isAllowed(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '/';
        foreach ($this->disallowRules as $rule) {
            if (str_starts_with($path, $rule)) {
                return false;
            }
        }
        return true;
    }
}
