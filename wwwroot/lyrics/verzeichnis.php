<?php

require_once( "../lib/template.inc.php" ); // $function->secureString(
require_once( "../lib/config.inc.php" );
require_once( "../lib/functions.inc.php");
require_once( "../lib/connection.inc.php");

$function 		= new Functions();
$design 		= new Template();
$config 		= new Config();

$letter		 	= $function->secureString(urldecode($_REQUEST['l']));
$page 			= $function->secureString(urldecode($_REQUEST['p']));
$title_dir_tag	= $config->lyrics_dir_title_tag(); 
$page_count		= $function->GetLettersCount($letter);
$cache_uri 	 	= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$max_pagecount	= $config->lyrics_dir_pagecount_per_page();

if (!isset($page) || !is_numeric($page)){
	$page 			= 0;
	$title_tag 		= substr($title_dir_tag, 0, 50) . " mit Eintrag ".strtoupper($letter);
	$h1_tag 		= "Lyrics und Songtexte mit dem Anfangsbuchstaben ".strtoupper($letter);
} else {
	$title_tag 		= substr($title_dir_tag, 0, 40) . " mit Eintrag ".strtoupper($letter). " - Seite $page";
	$h1_tag 		= "Lyrics und Songtexte mit dem Anfangsbuchstaben ".strtoupper($letter). " auf Seite $page";
}
$inhalte			= $function->GetLetterContent($letter,$page);

$letters =<<<LETTER
<div class="letters">
<h2><strong>$h1_tag</strong></h2><strong><i>
LETTER;

// AA-ZZ combinations
$preletter = substr($letter, 0, 1);
$foreach_count = 0;
foreach(range('A', 'Z') as $letter2) {
	$l 		= strtolower($preletter.$letter2);
	$lgro	= strtoupper($l);
	
	if ( $foreach_count >= 12 ){
		$foreach_count = 0;
		$letters .=<<<LETTER
			<br />
LETTER;
	}
	
	if ( strcasecmp($letter2,"Z") == 0 ){
		$letters .=<<<LETTER
<a href="/lyrics/verzeichnis.php?l=$l" class="url" itemprop="url" title="Lyrics mit $lgro als Beginn">$lgro</a>&nbsp;
LETTER;
	} else {
		$letters .=<<<LETTER
<a href="/lyrics/verzeichnis.php?l=$l" class="url" itemprop="url" title="Lyrics mit $lgro als Beginn">$lgro</a>&nbsp;-&nbsp;
LETTER;
	}
	$foreach_count++;
}

$letters .=<<<LETTER
</i></strong></h2></div><br />
LETTER;
$pages .=<<<PAGES
Seite: <div class="strong">&nbsp;&nbsp;&nbsp;
PAGES;

$flag = 0;
$countPageUp = 0;

for ($i=$page;$i<$page_count;$i++){
//echo "i=$i - page=$page - page_count=$page_count<br />";
	$inc = $function->GetLetterContent($letter,$i);
	if ( strlen($inc) >= 200 ){
		
		if ($i>0){
	
			if ( $i == 1 ){
				$rel=" rel=\"nofollow\" ";
			} else if ( $countPageUp == $max_pagecount ){
				$rel=" rel=\"prev\" ";
			} else {
				$rel=" rel=\"prefetch\" ";
			}
			$pages .=<<<PAGES
			<a href="/lyrics/verzeichnis.php?l=$letter&p=$i" $rel class="url" itemprop="url" title="$letter Songtext und Lyrics Verzeichnis Seite $i">$i</a>&nbsp;-&nbsp;
PAGES;

		//	if ( $countPageUp >= $max_pagecount && $flag == 0 ) {
		//		$pages .= "<br />";
		//		$flag = 1;
		//	}
			
			if ( $countPageUp >= $max_pagecount ) {
				
				$p = $i;
				$p++;
				$pages .=<<<PAGES
				<a href="/lyrics/verzeichnis.php?l=$letter&p=$p" class="url" itemprop="url" rel="next" title="$letter Songtext und Lyrics Verzeichnis Seite $i">&gt;</a>&nbsp;
PAGES;
				break;
			} 
		} // if ($i>0){
		$countPageUp++;
	} // if ( strlen($inhalte) >= 200 ){
}

$pages .=<<<PAGES
</div><br /><br />
PAGES;


if ( strlen($inhalte) < 200 ){
	$robots = "NOINDEX,FOLLOW";
} elseif (strlen($inhalte) >= 200) {
	$robots = "INDEX,FOLLOW,ALL";
};

$content = array_merge(
		array('title_html_de'=>$title_tag), 
		array('robots'=>$robots),
		array('letters'=>$letters),
		array('pages'=>$pages),
		array('content'=>$inhalte),
		array('description_de'=>$title_tag),
		array('keyword_de'=>"Songtext, Lyrics, Verzeichnis"),
		array('yt_title'=>$title_tag),
		array('canonical_tag'=>"http://".$cache_uri)
		);
		
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('directory_list', $content, true, 3600*24*3);

exit(0);

?>