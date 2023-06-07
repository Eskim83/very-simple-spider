<?php

/*
Autor: Maciej Włodarczak (https://eskim.pl)
Wersja: 1.0

Na podstawie artykułu: https://eskim.pl/pobieranie-strony-offline-w-php/
*/

include_once 'spider.php';

$dir = 'website/';
$url = 'https://example.com';
$linksfn = 'links.data';

$spider = new Spider($url, 'index.html');
if (file_exists($linksfn) ) {
	
	$links = json_decode (file_get_contents ($linksfn), true);
}

else {
	
	$links[ 'index.html' ] = [
		'link' => $url,
		'discover' => time(),
		'downloaded' => false
	];
}

if (!file_exists($dir)) mkdir($dir);

$counter = 0;
$count = count($links);

while ($counter < $count) {
	
	$key = key($links);
	if (!$links[$key]['downloaded']) {
		
		echo "Progress: $counter / $count \n";
		
		$page = $spider->getWebsite ( $links[$key]['link'] );
		$local = $spider->convertToLocal ( $page );
		
		file_put_contents( $dir.$key, $local );
		$links[$key]['downloaded'] = true;
		$spider->getLinks($page, $links);
		$count = count($links);
		file_put_contents( $linksfn, json_encode($links) );
		$spider->wait();
		
	}
	next($links);
	
	$counter++;
	
} 
?>