<!DOCTYPE html>
<html lang="en">
<head profile="http://dublincore.org" />
<title>{title_html_en}</title> 
<meta name="robots" content="noindex,follow" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="fb:admins" content="1414912982105983" />
<meta name="keywords" content="{keyword_de}, youtube umwandler,youtube mp3" />
<meta name="description" content="{description_en}" />
<meta charset="utf-8" />
<meta property="article:publisher" content="https://www.facebook.com/YoutubeConverter" />
<meta property="article:author" content="https://www.facebook.com/basti.enger" />
<meta name="twitter:creator" value="@SebastianEnger" />
<meta name="rating" content="general" />
<meta name="geo.position" content="51.0000,9.0000" />
<meta name="geo.placename" content="Industriegebiet Falkenstein-Siebenhitz, 08223 Falkenstein, Deutschland" />
<meta name="geo.region" content="EN" />

<link rel="stylesheet" href="/css/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/headlines.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/ui-lightness/jquery.ui.all.css" />
<link rel="stylesheet" href="/css/demo.css" />
<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
<link rel="canonical" href="{canonical_tag}" />
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<link rel="schema.DCTERMS" href="http://purl.org/dc/terms/" />
<link rel="alternate" media="handheld" href="http://www.youtube-mp3.mobi/m/" hreflang="de" />
<link rel="alternate" media="handheld" href="http://www.youtube-mp3.mobi/m/en/" hreflang="en" />

<script src="/js/jquery.min.js" type="text/javascript"></script>
<script src="/js/jquery-ui.min.js" type="text/javascript"></script>

<meta name="dcterms.Title" lang="EN" content="{title_html_en}" />
<meta name="dcterms.Subject" lang="EN" content="Youtube zu MP3 Konverter" />
<meta name="dcterms.Publisher" content="Youtube-MP3.MOBI, Ltd." />
<meta name="dcterms.Type" content="text/html" />
<meta name="dcterms.Description" lang="EN" content="{description_en}" />

<meta name="dcterms.Coverage" content="University of Applied Science Mittweida" />
<meta name="dcterms.Rights" content="Copyright 2013, Youtube-MP3.MOBI, Ltd., All rights reserved." />
<meta name="dcterms.Language" content="EN" />
<link rel="alternate" type="application/rss+xml" title="Youtube MP3 Download Feed - (RSS 2.0)" href="http://www.youtube-mp3.mobi/rss.php" />

<style type="text/css">
body {width: 100%; margin: 0; padding: 0;}
</style>


<style>
	body {
		text-align: center
	}
	#progressbar {
		width: 300px;
		margin: 20px auto;
	}
	.ui-progressbar-value {
		background-image: url(/images/pbar-ani.gif);
	}
</style>
<script>
	$(function() {
		var retVal;
		var status;
		var $pG = $('#progressbar').progressbar();
		var pGress = setInterval(function() {
			var pVal = $pG.progressbar('option', 'value');
			var pCnt = !isNaN(pVal) ? (pVal + 1) : 1;
			if (pCnt > 100) {
				clearInterval(pGress);
			} else {
				$pG.progressbar({
					value : pCnt,
					max: 99,
				});
				queryState();	
			}
		}, 700);
	});
	
	function queryState(){
		$.ajax({ //Process the form using $.ajax()
			type        : 'POST', //Method type
			url         : '/ajax/stateV2.php', 
			data: {
				language: $('#language').val(),
				video_id: $('#videoid').val(),
			},
			dataType	: "text",
			async		: true,
			success     : function(data,status) {
				if (data.toLowerCase().indexOf("http") >= 0){
					$('#downloadready').html(data);
				} else {
					$('#downloadready').html("Statusmeldung: "+data)
				}
				//alert(data);
			},
		});
		return 0;
	};
</script>

</head>
<body itemscope itemtype="http://schema.org/WebApplication" class="hmedia">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=1414912982105983";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div>
<div> 
	<h1 style="font-size: 16px;"><b><strong>
	&nbsp;♥&nbsp; <a href="http://www.youtube-mp3.mobi/" itemprop="url" class="url" rel="prefetch">Youtube MP3 Umwandeln</a> 
	&nbsp;♥&nbsp; <a href="http://www.youtube-mp3.mobi/app/" itemprop="url" class="url" rel="prefetch" title="YouTube Konverter - Android Apps bei Google Play als kostenloser Download">Android App</a> 
	&nbsp;♥&nbsp; <a class="url" href="http://www.buzzerstar.com/" itemprop="url">News</a>
	&nbsp;♥&nbsp; <a href="http://www.youtube-mp3.mobi/lyrics/" title="Verzeichnis für Lyrics" itemprop="url" class="url" rel="prefetch">Lyrics Verzeichnis</a> 
	&nbsp;♥&nbsp; <a class="url" href="http://www.youtube-mp3.mobi/news.php" rel="prefetch" itemprop="url">MP3s</a>
	&nbsp;♥&nbsp; <a href="http://www.youtube-mp3.mobi/rss.php" itemprop="url" class="url">Music RSS Feed</a> 
	&nbsp;♥&nbsp; <a href="http://www.youtube-mp3.mobi/en/" hreflang="en" itemprop="url" class="url" title="Legal YouTube to MP3 Converter in English language"><img class="photo" src="/images/en.gif" alt="Legal YouTube to MP3 Converter in English language" width="20" height="13" /></a>
	</strong></b></h1>
</div>
<div style="float:clear;"></div>

<div class="content">
	<ul style="list-style: none;" id="target_search">
		<h1 itemprop="headline" class="fn"><strong><b>{title_content} MP3 Download with Lyrics</b></strong></h1>
		<br />
		<noscript>
		<h1>You <b>dont have</b> Javascript enabled - please read the following hints:</h1><br /><br />
		We use the jQuery Javascript Modul, to convert the Youtube Videos to MP3 Music.<br />
		<b><strong><a href="http://www.enable-javascript.com/" rel="nofollow" target="_blank">Please enable Javascript for this Website!</a></strong></b><br />
		</noscript>
		<li>
			<div id="progressbar"></div>
			<div id="downloadready"></div>
			<input type="hidden" id="videoid" value="{video_id}" />
			<input type="hidden" id="language" value="EN" />
			<br /><br />
		</li>				
		<li itemscope itemtype="http://schema.org/MusicRecording">
			{embedding_content}
			<br /><br />
			<h2 class="haudio"><span itemprop="byArtist" class="contributor">{songtext_artist}</span> - <strong>Songtext <b itemprop="name" class="fn">{songtext_title}</b> Lyrics</strong>{duration}</h2>
			<br />
			<p itemprop="description" lang="{songtext_lang}">{songtext_content}</p>
			<br />
		</li>
		<li>
			<span class="contributor vcard">
				Author</a>: <span class="fn">{author_content}</span>
			</span>
		</li>
		<li>Category: {category_content}</li>
		<li>Video Length: {lenght_content} seconds</li>
		<li>Video Views: {views_content}</li>
		<li>
			<br /><br />
			<strong><b>
			Please link us on the Internet, like us on Facebook&nbsp;♥&nbsp;, Share on Google Plus&nbsp;♥&nbsp;, share Pinterest&nbsp;♥&nbsp; and exchange on Twitter&nbsp;♥&nbsp; about us.
			<br /><br />
			<div class="fb-like" rel="nofollow" data-href="https://www.facebook.com/YoutubeConverter" data-layout="standard" data-action="like" data-show-faces="false" data-share="true"></div>
			<div class="fb-comments" data-href="{canonical_tag}" rel="nofollow" data-width="450" data-numposts="3" data-colorscheme="light"></div>
			</b></strong>
			<br /><br />
		</li>	
		<li><iframe src="{framesource}" height="10" width="100" style="display:none; visibility: hidden;" id="converting frame" name="converter frame"></iframe></li>
		<li>&copy; 2014 - www.youtube-mp3.mobi &nbsp;-&nbsp;<a href="https://www.buzzerstar.com/" hreflang="de" target="_blank">Funny Videos</a> - <a
href="https://7lol.de/" title="Lustige Bilder" hreflang="de" target="_blank">Funny Pictures</a> - <a
href="https://www.buzzerstar.com/kategorie/Entertainment">Entertainment</a> - <a
href="https://www.buzzerstar.com/kategorie/Geschichten">Tolle Geschichten</a> - <a
href="https://www.buzzerstar.com/development/">Technologie</a> - <a href="https://blog.onetopp.com/" hreflang="en" target="_blank">Technology Blog</a> - <a href="https://www.onetopp.com/" hreflang="en" target="_blank">Innovation</a> - <a href="https://devop.tools/" hreflang="de" target="_blank">Devops</a> <a class="url" href="/imprint.php" rel="nofollow" target="_blank" itemprop="url">Imprint</a> - <a class="url" href="/privacy-policy.php" rel="nofollow" target="_blank" itemprop="url">Privacy Policy</a></li>
	</ul>
</div>

<ul>
	<li>
	<script type="text/javascript">
		function addLink() {
			var body_element = document.getElementsByTagName('body')[0];
			var selection;
			selection = window.getSelection();
			var pagelink = "<br /><br /> Mehr Youtube Videos mit MP3s und Songtexten downloaden auf: <a href='http://www.youtube-mp3.mobi/' hreflang='de'>Youtube zu Mp3 Umwandeln</a> - <a href='"+document.location.href+"'>"+document.location.href+"</a>";
			var copytext = selection + pagelink;
			var newdiv = document.createElement('div');
			newdiv.style.position='absolute';
			newdiv.style.left='-99999px';
			body_element.appendChild(newdiv);
			newdiv.innerHTML = copytext;
			selection.selectAllChildren(newdiv);
			window.setTimeout(function() {
				body_element.removeChild(newdiv);
			},0);
		}
		document.oncopy = addLink;
		</script>
	</li>
	<li>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-49319016-1', 'youtube-mp3.mobi');
		  ga('send', 'pageview');

		</script>
	</li>
	<li>
		<!-- Piwik -->
		<script type="text/javascript">
		  var _paq = _paq || [];
		  _paq.push(['trackPageView']);
		  _paq.push(['enableLinkTracking']);
		  (function() {
			var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.youtube-mp3.mobi/l/";
			_paq.push(['setTrackerUrl', u+'piwik.php']);
			_paq.push(['setSiteId', 1]);
			var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
			g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
		  })();

		</script>
		<noscript><p><img src="http://www.youtube-mp3.mobi/l/piwik.php?idsite=1" style="border:0;" alt="Youtube-MP3.Mobi Video Konverter" /></p></noscript>
		<!-- End Piwik Code -->
	</li>
</ul>

</body>
</html>