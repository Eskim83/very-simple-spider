<?php

/*
Autor: Maciej Włodarczak (https://eskim.pl)
Wersja: 1.0

Na podstawie artykułu: https://eskim.pl/pobieranie-strony-offline-w-php/
*/

class Spider {

	private $last_url = '';
	private $base_url = '';
	private $start_page_name = '';
	
	function __construct ($url, $start_page_name = 'index.html') {

		$this->base_url = $url;
		$this->start_page_name = $start_page_name;
	}
	
	function filename($url) {
		
		$url = str_replace($this->base_url,'',$url);
		return preg_replace("/[^a-z0-9\_\-\.]/i", '', $url).'.html';
	}
	
	function encodeLink($url, $leaveHashtag = false) {
		
		$matches = [];
		$url = strtolower (trim ($url));
		if (preg_match('/.+?(?=#)/', $url, $matches) ) {
			
			if ($matches[0] == $this->base_url) {
				
				if ($leaveHashtag) return str_replace($matches[0], $this->start_page_name, $url);
				return $this->start_page_name;
			}
			
			if ($leaveHashtag) return str_replace ($matches[0], $this->filename($matches[0]), $url);
			return $this->filename($matches[0]);
		}
		return $this->filename ($url);
	}
	
	function clearLink($url) {
		
		$matches = [];
		$url = trim ($url);
		if (preg_match('/.+?(?=#)/', $url, $matches) ) return $matches[0];
		return $url;
	}

	function getWebsite ($url) {

		if (!$this->isInternalURL($url)) return false;
		echo "Downloading $url\n";
		do {

			$opts = [
				'http'=> [
					'method'=>"GET",
					'header'=>"Accept-Encoding: gzip, deflate, br\r\n" .
							  "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\n" .
							  "Referer: https://eskim.pl\r\n" .
							  "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 Edg/113.0.1774.50\r\n",
					"follow_location" => false
				],
			];

			$context = stream_context_create ($opts);
			$compressed = file_get_contents ($url, false, $context);
			
			
			$pattern = "/^Location:\s*(.*)$/i";
			$location_headers = preg_grep($pattern, $http_response_header);
			
			//print_r($location_headers);
			if (!empty($location_headers) && preg_match($pattern, array_values($location_headers)[0], $matches)) {
				$url = $matches[1];
				
				if (!$this->isInternalURL($url)) return false;
				$repeat = true;
			}
			else {
				$repeat = false;
			}
			
		} while ($repeat);
		
		$this->last_url = $url;
		if (!$this->isInternalURL($url)) return false;
		
		$webpage = gzdecode ($compressed);
		if ($webpage === false) return $compressed;

		return $webpage;
	}


	function isInternalURL ($url) {

		if ( stripos ($url, $this->base_url) === 0 && stripos ($url, $this->base_url.'.') === false ) {
			return true;
		}
		return false;
	}
	
	function getLinks ($page, &$linkstbl = null) {

		$links = [];

		$website = new DOMDocument();
		$website->loadHTML ($page); // zamień stronę na obiekt DOM

		$as = $website->getElementsByTagName('a'); // pobierz wszystkie tagi a

		foreach ($as as $a) {

			if ($a->hasAttribute('href')) {

				$tlink = $this->clearLink($a->getAttribute('href'));
				if ($tlink == $this->base_url) continue;
				if ($this->isInternalURL($tlink)) $links[] = $tlink;
			}
		}
		
		if ($linkstbl === null) return array_unique ($links);

		foreach (array_unique ($links) as $l) {
			
			$link = $this->clearLink ($l);
			$enclink = $this->encodeLink($link);
			
			if (!isset( $linkstbl [$enclink] ) ) {
				
				$linkstbl [$enclink] = [
				
					'link' => $link,
					'discover' => time(),
					'downloaded' => false
				];
			}
		}
	}
	
	function convertToLocal ($page) {

		$links = [];

		$website = new DOMDocument();
		$website->loadHTML ($page); // zamień stronę na obiekt DOM

		$as = $website->getElementsByTagName('a'); // pobierz wszystkie tagi a

		foreach ($as as $a) {

			if ($a->hasAttribute('href')) {

				$link = trim( $a->getAttribute('href') );
				if ($link == $this->base_url) $a->setAttribute('href', $this->start_page_name);
				else $a->setAttribute('href', $this->encodeLink($link, true));
			}
		}
		
		return $website->saveHTML();
	}	
	
	public function wait() {
	
		$time = rand(1000000,5000000);
		usleep($time);
	}
}