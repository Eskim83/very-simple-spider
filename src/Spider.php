<?php

namespace VerySimpleSpider;

use Closure;
use VerySimpleSpider\Support\Downloader;
use VerySimpleSpider\Support\Logger;

class Spider
{
    protected string $startUrl;
    protected array $visited = [];
    protected array $queue = [];
    protected int $maxDepth = 3;
    protected ?int $maxPages = null;
    protected Logger $logger;
    protected Downloader $downloader;
    protected ?Closure $onPageDownloaded = null;
    protected ?Closure $urlFilter = null;

    public function __construct(string $startUrl, int $maxDepth = 3, Logger $logger = null, Downloader $downloader = null)
    {
        $this->startUrl = $startUrl;
        $this->maxDepth = $maxDepth;
        $this->logger = $logger ?? new Logger(true);
        $this->downloader = $downloader ?? new Downloader();
        $this->queue[] = ["url" => $startUrl, "depth" => 0];
    }

    public function setOnPageDownloaded(callable $callback): void
    {
        $this->onPageDownloaded = Closure::fromCallable($callback);
    }

    public function setUrlFilter(callable $callback): void
    {
        $this->urlFilter = Closure::fromCallable($callback);
    }

    public function setMaxPages(int $limit): void
    {
        $this->maxPages = $limit;
    }
	
	protected array $ignoredExtensions = [
		'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp',
		'ico', 'woff', 'woff2', 'ttf', 'eot', 'otf', 'pdf', 'zip', 'rar',
		'mp3', 'mp4', 'avi', 'mov', 'webm', 'json', 'xml'
	];
	
	public function setIgnoredExtensions(array $extensions): void
	{
		$this->ignoredExtensions = array_map('strtolower', $extensions);
	}
	
	public function addIgnoredExtensions(array $extensions): void
	{
		$this->ignoredExtensions = array_unique(array_merge(
			$this->ignoredExtensions,
			array_map('strtolower', $extensions)
		));
	}

    public function run(): void
    {
        $pageCount = 0;

        while (!empty($this->queue)) {
            if ($this->maxPages !== null && $pageCount >= $this->maxPages) {
                $this->logger->log("[!] Max page limit reached ({$this->maxPages})");
                break;
            }

            $current = array_shift($this->queue);
            $url = $current["url"];
            $depth = $current["depth"];

            if (isset($this->visited[$url]) || $depth > $this->maxDepth) {
                continue;
            }

            if ($this->urlFilter && !($this->urlFilter)($url)) {
                $this->logger->log("[x] Skipped by filter: $url");
                continue;
            }

            $this->visited[$url] = true;
            $pageCount++;

            $this->logger->log("[*] Crawling: $url (depth $depth)");

			$html = $this->downloader->fetch($url);

			if ($html === null) {
				$this->logger->log("[!] Failed to fetch: $url");
				continue;
			}

			if ($this->onPageDownloaded) {
				($this->onPageDownloaded)($url, $html);
			}

			$links = $this->extractLinks($url, $html);

			foreach ($links as $link) {
				$ext = strtolower(pathinfo(parse_url($link, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
				if ($ext && in_array($ext, $this->ignoredExtensions, true)) {
					$this->logger->log("[i] Skipped binary/link: $link");
					continue;
				}

				if (!isset($this->visited[$link])) {
					$this->queue[] = ["url" => $link, "depth" => $depth + 1];
				}
			}
        }
    }

    protected function extractLinks(string $baseUrl, string $html): array
    {
        $links = [];
        if (!preg_match_all('/href=["\']?([^"\'>\s]+)/i', $html, $matches)) {
            return $links;
        }

        foreach ($matches[1] as $href) {
            $absolute = $this->resolveUrl($baseUrl, $href);
            if (parse_url($absolute, PHP_URL_HOST) === parse_url($this->startUrl, PHP_URL_HOST)) {
                $links[] = $absolute;
            }
        }

        return array_unique($links);
    }

	protected function resolveUrl(string $base, string $rel): string
	{
		// 1. Absolutny URL
		if (parse_url($rel, PHP_URL_SCHEME)) {
			return $rel;
		}

		// 2. Schemaless (np. //cdn.domain.com)
		if (str_starts_with($rel, '//')) {
			$scheme = parse_url($base, PHP_URL_SCHEME) ?? 'http';
			return $scheme . ':' . $rel;
		}

		// 3. Anchory i query
		if (str_starts_with($rel, '#') || str_starts_with($rel, '?')) {
			return $base . $rel;
		}

		// 4. Względna ścieżka
		$parts = parse_url($base);
		$scheme = $parts['scheme'] ?? 'http';
		$host = $parts['host'] ?? '';
		$port = isset($parts['port']) ? ":{$parts['port']}" : '';
		$path = isset($parts['path']) ? dirname($parts['path']) : '';

		$abs = "$scheme://$host$port" . rtrim("$path/$rel", '/');
		return preg_replace('#/\\./|/[^/]+/\\.\\./#', '/', $abs);
	}

}
