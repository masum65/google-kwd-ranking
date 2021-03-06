<?php

	class Crawler {
		
		//public	$gsrc_url	=	'https://www.google.com/search?num=100&q=';
		public	$gsrc_url	=	'https://www.google.com/search?num=100';
		public	$cHeadres	=	array(
							'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
							'Accept-Language: en-US,en;q=0.5',
							'Connection: Keep-Alive',
							'Pragma: no-cache',
							'Cache-Control: no-cache'
						);

		function __construct() {
			//	echo	'Just checking!';
		}
		

		function dlPage($href) {
			global	$cHeadres;

			$ch	=	curl_init();
			if($ch){
				curl_setopt($ch, CURLOPT_URL, $href);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->cHeadres);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
				curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
				$str = curl_exec($ch);
				curl_close($ch);

				$dom = new simple_html_dom();
				$dom->load($str);
				return $dom;
			}
		}

		function LoadPage($kwd, $srcterm, $page=1, $startfrom=0) {
			
			include_once	"simple_html_dom.php";
			
			$finallink	=	$this->gsrc_url . '&p=' . $page . '&start=' . $startfrom . '&q=' . urlencode($kwd);

			//	Two methods for loading the page
			$maincode	=	file_get_html($finallink);
			//$maincode	=	$this->dlPage($finallink);
			
			$position	=	0;
			$counter	=	0;
			$resarray	=	array();
			//foreach ($maincode->find('h3[class=r] cite') as $post) {
			foreach ($maincode->find('div[class=kv] cite') as $post) {
				$position++;
				if (strpos($post->plaintext, $srcterm) !== false) {
					
					//	Array with Keyword and Position
					//array_push($resarray, [$kwd,$position,$post->plaintext]);
					
					//	Keep only position
					array_push($resarray, ((($page -1) * 100) + $position));
					
					//	Break if searched
					//break;
					
					//	Allow top 5 positions only
					$counter++;
					if ($counter >= 5) {
						break;
					}
				}
			}
			
			return	$resarray;
		}
	}

?>