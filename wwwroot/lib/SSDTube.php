<?php
/*

	The MIT License (MIT)

	Copyright (c) 2012 Sebastian Sulinski : www.ssdtutorials.com
	
	Permission is hereby granted, free of charge, to any person 
	obtaining a copy of this software and associated documentation files 
	(the "Software"), to deal in the Software without restriction, 
	including without limitation the rights to use, copy, modify, merge, 
	publish, distribute, sublicense, and/or sell copies of the Software, 
	and to permit persons to whom the Software is furnished to do so, 
	subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in all copies 
	or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
	IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
	DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
	ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	
*/
class SSDTube {
		
		public $url;
        public $id;
        public $list;
        
        public $title;
        public $author;
        public $content;
        public $category;
        public $thumbnail_0_url;
        public $thumbnail_0_width;
        public $thumbnail_0_height;
        public $thumbnail_1_url;
        public $thumbnail_1_width;
        public $thumbnail_1_height;
        public $keywords;
        public $duration;
        public $viewcount;
        
        public $validationOutput;
        
        private $invalidId = 'Invalid id';
        
        private $validateUrl = 'http://gdata.youtube.com/feeds/api/videos/';
        private $namespaceMedia = 'http://search.yahoo.com/mrss/';
        private $namespaceYt = 'http://gdata.youtube.com/schemas/2007';
        
        private $urls = array(
        	'youtube.com', 'youtu.be', 'www.youtube.com', 'www.youtu.be'
        );
        
        // read more : http://code.google.com/apis/youtube/player_parameters.html
        private $playerParams = array(
        	'autohide' => 1,
        	'autoplay' => 0,
			'border' => '',
			'cc_load_policy' => '',
			'color' => '',
			'color1' => '',
			'color2' => '',
			'controls' => '',
			'disablekb' => '',
			'enablejsapi' => '',
			'egm' => '',
			'fs' => '',
			'hd' => '',
			'iv_load_policy' => '',
			'loop' => '',
			'modestbranding' => '',
			'origin' => '',
			'playerapiid' => '',
			'playlist' => '',
			'rel' => 1,
			'showinfo' => '',
			'showsearch' => '',
			'start' => '',
			'theme' => '',
			'version' => 3
        );
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function identify($url = null, $check = false) {
        
        	if (!empty($url)) {
        		
        		// reset all properties
	        	// to allow to use the same class instance
	        	// in order to display multiple videos on one page
	        	$this->url = $url;
	        	$this->id = null;
	        	$this->list = null;
	        	$this->title = null;
	        	$this->author = null;
	        	$this->content = null;
	        	$this->category = null;
	        	$this->thumbnail_0_url = null;
	        	$this->thumbnail_0_width = null;
	        	$this->thumbnail_0_height = null;
	        	$this->thumbnail_1_url = null;
	        	$this->thumbnail_1_width = null;
	        	$this->thumbnail_1_height = null;
	        	$this->keywords = null;
	        	$this->duration = null;
	        	$this->viewcount = null;
	        	$this->validationOutput = null;
	        		
        		if ($this->isUrl($this->url)) {
        			
        			$url = $this->url;
        			
	        		$urlString = parse_url($this->url, PHP_URL_QUERY);
	        		    		
					parse_str($urlString, $urlArgs);
					
					if (array_key_exists('list', $urlArgs)) {
						$this->list = $urlArgs['list'];
						$urlNew = explode('?', $this->url);
						$url = count($urlNew) > 1 ? array_shift($urlNew) : $this->url;
					}
					
					// if old, long url i.e.
					// http://www.youtube.com/watch?v=lh-hQitgTg8&list=UU5UxFOgMmvEM4G49mIsLJZA
					if (array_key_exists('v', $urlArgs)) {
					
						$this->id = $urlArgs['v'];
						
						// if embedding on the go
	        			// first process the 
						if ($check) {
							$this->id = $this->validate($this->id) ? $this->id : null;
						}
					
					// if new, short url i.e
					// http://youtu.be/lh-hQitgTg8?list=UU5UxFOgMmvEM4G49mIsLJZA 				
					} else {
					
						$urlSegments = explode('/', $url);
						$this->id = count($urlSegments) > 1 ? array_pop($urlSegments) : $urlSegments[0];						
						
						// if embedding on the go
	        			// first process the 
						if ($check) {
							$this->id = $this->validate($this->id) ? $this->id : null;
						}							
						
					}
					
				} else {
				
					$this->id = $this->validate($this->url) ? $this->url : null;
				
				}
        		
        	}
        	
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function embed($id = null, $list = null, $width = 560, $height = 315) {
        	
        	$id = !empty($id) ? $id : $this->id;
        	$list = !empty($list) ? $list : $this->list;        	
        	$params = $this->getParams($list);
        	
        	if (!empty($id)) {
        		
        		$player  = '<object width="'.$width.'" height="'.$height.'">';
        		$player .= '<param name="movie" value="http://www.youtube.com/v/';
        		$player .= $id;
        		$player .= $params;
        		$player .= '">';
        		$player .= '</param>';
        		$player .= '<param name="wmode" value="transparent"></param>';
        		$player .= '<param name="allowFullScreen" value="true">';
        		$player .= '<embed src="http://www.youtube.com/v/';
        		$player .= $id;
        		$player .= $params;
        		$player .= '" allowfullscreen="true" ';
        		$player .= 'type="application/x-shockwave-flash" wmode="transparent" ';
        		$player .= 'width="'.$width.'" height="'.$height.'"></embed>';
        		$player .= '</object>';
        		
        		return $player; 
        		
        	} else {
        		
        		return 'The video id is incorrect';
        		
        	}
        	
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function setParams($array = null) {
        	if (!empty($array) && is_array($array)) {
        		foreach($array as $key => $value) {
        			if (array_key_exists($key, $this->playerParams)) {
        				$this->playerParams[$key] = $value;
        			}
        		}
        	}
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        private function getParams($list = null) {
        	if (!empty($this->playerParams)) {
        		$out = array();
        		foreach($this->playerParams as $key => $value) {
        			if (!empty($value) || is_numeric($value)) {
        				$out[] = $key.'='.$value;
        			}
        		}
        		return !empty($list) ? '?list='.$list.'&'.implode('&', $out) : '?'.implode('&', $out);
        	}
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function validate($id = null) {
        	if (!empty($id)) {
        		$cUrl = curl_init();
        		curl_setopt($cUrl, CURLOPT_URL, $this->validateUrl.$id);
		        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
		        $this->validationOutput = curl_exec($cUrl);
		        curl_close($cUrl);
		        if (!empty($this->validationOutput) && $this->validationOutput != $this->invalidId) {
		        	$domDoc = new DOMDocument('1.0', 'utf-8');
					$domDoc->formatOutput = true;
				    $domDoc->loadXML($this->validationOutput);
				    
				    $this->title = 		$domDoc->getElementsByTagName('title')->
				    					item(0)->
				    					nodeValue;
				    					
					$this->author = 	$domDoc->getElementsByTagName('author')->
										item(0)->
										getElementsByTagName('name')->
										item(0)->
										nodeValue;
											
					$this->content = 	$domDoc->getElementsByTagName('content')->
										item(0)->
										nodeValue;
										
					$this->category =	$domDoc->getElementsByTagNameNS($this->namespaceMedia, 'category')->
										item(0)->
										nodeValue; 
										
										
										
					$this->thumbnail_0_url = $domDoc->getElementsByTagNameNS($this->namespaceMedia, 'thumbnail')->
										item(0)->getAttribute('url');
										
					$this->thumbnail_0_width = $domDoc->getElementsByTagNameNS($this->namespaceMedia, 'thumbnail')->
										item(0)->getAttribute('width');
										
					$this->thumbnail_0_height = $domDoc->getElementsByTagNameNS($this->namespaceMedia, 'thumbnail')->
										item(0)->getAttribute('height');
										
					$this->thumbnail_1_url = $domDoc->getElementsByTagNameNS($this->namespaceMedia, 'thumbnail')->
										item(1)->getAttribute('url');
										
					$this->thumbnail_1_width = $domDoc->getElementsByTagNameNS($this->namespaceMedia, 'thumbnail')->
										item(1)->getAttribute('width');
										
					$this->thumbnail_1_height = $domDoc->getElementsByTagNameNS($this->namespaceMedia, 'thumbnail')->
										item(1)->getAttribute('height');
										
										
										
					$this->keywords =	$domDoc->getElementsByTagNameNS($this->namespaceMedia, 'keywords')->
										item(0)->
										nodeValue;  
										
					$this->duration =	$domDoc->getElementsByTagNameNS($this->namespaceYt, 'duration')->
										item(0)->getAttribute('seconds');
										
					$this->viewcount =	$domDoc->getElementsByTagNameNS($this->namespaceYt, 'statistics')->
										item(0)->getAttribute('viewCount');
																				    	 
		        	return true;
		        }	        
		        return false;
        	}
        	return false;
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function isUrl($url = null) {
			if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) {
				$urlParse = parse_url("http://www.youtube.com/watch?v=Sv5iEK-IEzw");
				if (array_key_exists('host', $urlParse) && in_array($urlParse['host'], $this->urls)) {
					return true;
				}
				return false;
			} else {
				return false;
			}
	  	}
        
        
        
        
        
        
        
        
        
        
        
        
        
             
}