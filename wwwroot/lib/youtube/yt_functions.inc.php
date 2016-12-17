<?php
ob_start();// if not, some servers will show this php warning: header is already set in line 46...

// Source: https://github.com/jeckman/YouTube-Downloader
require_once( "curl.inc.php");

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . '' . $units[$pow]; 
} 

function parseVideoID( $request ){

	if(isset($request)) {
		$my_id = $request;
		if(strlen($my_id)>11){
			$url   = parse_url($my_id);
			$my_id = NULL;
			if( is_array($url) && count($url)>0 && isset($url['query']) && !empty($url['query']) ){
				$parts = explode('&',$url['query']);
				if( is_array($parts) && count($parts) > 0 ){
					foreach( $parts as $p ){
						$pattern = '/^v\=/';
						if( preg_match($pattern, $p) ){
							$my_id = preg_replace($pattern,'',$p);
							return $my_id;
							break;
						}
					}
				}
				if( !$my_id ){
				//	echo '<p>No video id passed in</p>';
				//	exit;
				}
			}else{
			//	echo '<p>Invalid url</p>';
			//	exit;
			}
		}
	} else {
	//	echo '<p>No video id passed in</p>';
	//	exit;
	}
	return $my_id;
}

function getDownloadLink( $my_id ){
	$thumbnail_url = $title = $redirect_url = $content_type = $my_formats_array = $url_encoded_fmt_stream_map = $type = $url = '';
	
	$my_video_info = 'http://www.youtube.com/get_video_info?&video_id='. $my_id;
	$my_video_info = curlGet($my_video_info);
	parse_str($my_video_info);

	if(isset($url_encoded_fmt_stream_map)) {
		/* Now get the url_encoded_fmt_stream_map, and explode on comma */
		$my_formats_array = explode(',',$url_encoded_fmt_stream_map);
	}	

	/* create an array of available download formats */
	$avail_formats[] = '';
	$i = 0;
	$ipbits = $ip = $itag = $sig = $quality = '';
	$expire = time(); 

	foreach($my_formats_array as $format) {
		parse_str($format);
		$avail_formats[$i]['itag'] = $itag;
		$avail_formats[$i]['quality'] = $quality;
		$type = explode(';',$type);
		$avail_formats[$i]['type'] = $type[0];
		$avail_formats[$i]['url'] = urldecode($url) . '&signature=' . $sig;
		parse_str(urldecode($url));
		$avail_formats[$i]['expires'] = date("G:i:s T", $expire);
		$avail_formats[$i]['ipbits'] = $ipbits;
		$avail_formats[$i]['ip'] = $ip;
		$i++;
	}

	/* here we leave out WebM video and FLV - looking for MP4 */
	$target_formats = array('37','22','18','17', '38', '46', '45', '35' );

	/* Now we need to find our best format in the list of available formats */
	$best_format = '';
	for ($i=0; $i < count($target_formats); $i++) {
		for ($j=0; $j < count ($avail_formats); $j++) {
			if($target_formats[$i] == $avail_formats[$j]['itag']) {
				//echo '<p>Target format found, it is '. $avail_formats[$j]['itag'] .'</p>';
				$best_format = $j;
				break 2;
			}
		}
	}

	$cleanedtitle = clean($title);
	//echo '<p>Out of loop, best_format is '. $best_format .'</p>';
	if( (isset($best_format)) && 
	  (isset($avail_formats[$best_format]['url'])) && 
	  (isset($avail_formats[$best_format]['type'])) 
	  ) {
		$redirect_url = $avail_formats[$best_format]['url'].'&title='.$cleanedtitle;
		$content_type = $avail_formats[$best_format]['type'];
	}
	
	return $redirect_url;
}

function isYoutubeVideo($string){
	
	$regYoutube = "/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/i";
	//$regYoutube = "/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/";
	$regVimeo = "/^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/";
	$regDailymotion = "/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/";
	$regMetacafe = "/^.*(metacafe\.com)(\/watch\/)(\d+)(.*)/i";
	
	if (preg_match($regYoutube,$string) ){
		return 1;
	} else {
		return 0;
	}
}

?>