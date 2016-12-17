<?php
	//--------------------------------------------------------------------------------
	//	RSS2Writer (v2.1) and sample controller
	//
	//	(c) Copyright Daniel Soutter
	//	
	//	Blog: http://www.code-tips.com/
	//	
	//  This script may be used or developed at your own risk as long as it is 
	//	referenced appropriately.
	//
	//	This script may be used or developed at your own risk as long as it is 
	//	referenced appropriately.
	//	Please post any comments or suggestions for improvement to 
	//	my blog (above).
	//
	//	For technical information about this Php RSS2Writer (v2.1) class, see: 
	//	http://www.web-resource.org/web-programming/free-php-scripts/RSS2Writer/
	//
	//	For usage instructions for the Php RSS2Writer (v2.1) class, see: 
	//	http://www.code-tips.com/2010/01/php-rss2writer-v20-generate-rss-20-feed.html
	//--------------------------------------------------------------------------------

	/*********************************************************************************
	*	Usage Notes:
	**********************************************************************************
	*
	* 	Optional Channel Elements
	*
	*  	To add an additional channel element: 
	*	- 	Call the addElement function, passing the element name and value 
	*		as string paramaters, and/or
	* 	-	Call the channelImage, channelCloud or addCategories functions	
	*	-----------------------------------------------------------------------------
	*		language		-	The language used in the feed
	*		copyright 		-	Copyright notice
	*		managingEditor	-	Email address for editor of content in the feed
	*		webMaster		-	Email address for technical issues relating to the feed
	*		pubDate			-	The latest publish date of the feed (must be > feed pubDate)
	*								date("D, d M Y H:i:s e") - 	current date formatted appropriately
	*		lastBuildDate	-	The date content was last updated
	*		generator		-	The software used to generate the channel (String)
	*		docs			-	URL to documentation about the rss format used
	*		ttl				-	Time to live (number of mins to cache before refresh)
	*		rating			-	PICS rating
	*		textInput		-	Text input displays with the channel
	*		skipHours		-	Skip refresh on specific hours
	*		skipDays		-	Skip refresh on specific days
	*	-----------------------------------------------------------------------------
	*
	*	Optional Item Elements:
	*
	*	Note: All elements of an RSS 2.0 item are optional, however one of the title or 
	*	description elements are required.  The addItem function of the RSS2Writer class 
	*	allows null values for the title, description and link paramaters, but at least one 
	*	must contain a value for the RSS 2.0 xml to be valid.
	*
	*  	Add an additional category to an item: 
	*	1.	Call the addCategory function after calling the addItem function, passing
	*		the category name as a string, with an option second paramater as the domain
	*		associated with the category
	*
	*  	Add an additional element to an item: 
	*	1. 	Call the addElement function, passing the element name and value 
	*		as string paramaters after calling the addItem function

	*	-----------------------------------------------------------------------------
	*		author			-	Email address for the author of the article
	*		pubDate			-	The publish date of the item
	*								date("D, d M Y H:i:s e") - 	current date formatted appropriately
	*
	*		comments		-	A url of a page with comments relating to the item
	*		enclosure		-	Descripbe a media object attached to the item
	*		guid			-	a unique identifier for the item (Required for Valid Atom)
	*		source			-	The rss channel the item came from
	*	-----------------------------------------------------------------------------
	*
	********************************************************************************/
	
	
	
	//Include RSS2Writer class file
	//--------------------------------
	require_once("RSS2Writer.php");
		
	//1. Instantiate new RSS2Writer, passing the title, description and link
	//--------------------------------
	$rss = new RSS2Writer(
		'Php RSS2Writer Class v2.0', 	//Feed Title
		'Generate an RSS 2.0 compatible Feed from website or database content', //Feed Description
		'http://www.web-resource.org/web-programming/free-php-scripts/RSS2Writer/', //Feed Link
		6, //indent
		false //Use CDATA
		);
	
	//Add channel data to the feed
	$rss->addCategory("RSS Feed");
	$rss->addCategory("Free Php Script");
	$rss->addCategory("Php: Generate RSS 2.0");
	//Optional Elements
	$rss->addElement('copyright', '(c) Daniel Soutter 2010');
	$rss->addElement('generator', 'Php RSS2Writer by Daniel Soutter');
	
	
	
	//2. Add items to the rss feed channel, passing the title, description and link
	//--------------------------------
	
	//Example Item 
	$rss->addItem('Php RSS2Writer Usage Instructions', 'Examples and instructions for using the Php RSS2Writer Class by Daniel Soutter', 'http://www.code-tips.com/');
	//Add categories to the item
	$rss->addCategory("Free Php Script");
	$rss->addCategory("Php: Generate RSS 2.0");
	$rss->addCategory("Php RSS2Writer Usage Instructions");
	//Optional Elements
	$rss->addElement('author', 'daniel@webmasterhub.net (Daniel Soutter)');
	
	//Example Item
	$rss->addItem('Php RSS2Writer Download Page', 'Item 3 description', 'http://www.web-resource.org/web-programming/free-php-scripts/RSS2Writer/');
	
	//Add categories to the item
	$rss->addCategory("Free Php Script");
	$rss->addCategory("Php: Generate RSS 2.0");
	$rss->addCategory("Php RSS2Writer Download Page");
	//Optional Elements
	$rss->addElement('author', 'daniel@webmasterhub.net (Daniel Soutter)');
	$rss->addElement('comments', 'http://www.code-tips.com/');
	
	
	
	//3. Output the RSS Feed
	//--------------------------------
	//$rss->writeToFile('rss.xml');		//write the xml output to file
	echo $rss->getXML();				//send the xml output to the user/browser (interpreted as an rss feed)

?>