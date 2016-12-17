<?php
if (!defined('IN_OI6G92B86HTS'))
	die ('Acces denied');

class zDirectory
{
	var $letter; // upper case
	var $category;
	var $display_cat;
	var $page;
	var $order_sql;
	var $order_link;
	var $total_results;
	public static $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	function __construct($letter, $category, $page)
	{
		$this->letter = $letter;
		$this->category = $category;
		$this->display_cat = GetDisplayCategory($this->category);
		$this->page = $page;
		if (isset($_GET['s']) && $_GET['s'] == 'name') {
			$this->order_sql = 'name ASC';
			$this->order_link = '&s=name';
		}
		else {
			$this->order_sql = 'entrydate DESC';
			$this->order_link = ''; // default setting
		}
	}
	
	function GetLetters()
	{
		global $subdomain;
		global $domain_ending;
		$first = $this->letter[0];
		if (is_numeric($first))
			return '';
		
		// read from cache
		//if ($cached)
		//{
			global $page_lang;
			$query = "SELECT letters FROM letters_$page_lang WHERE category = '$this->category' AND letter = '$first' LIMIT 1";
			$result = mysql_query($query);
			if ($row = mysql_fetch_object($result))
				return $row->letters;
		//}
		
		/* we use our cache only
		$second = $this->letter[1];
		$content = '';
		for ($i = 0; $i < strlen(zDirectory::$alphabet); $i++)
		{
			if (zDirectory::$alphabet[$i] == $second)
				$content .= " $first$second";
			else {
				$letter_small = strtolower($first.zDirectory::$alphabet[$i]);
				$letter_big = $first.zDirectory::$alphabet[$i];
				if (EntriesExist($letter_big))
					$content .= " <a href=\"http://$subdomain.zoozle.$domain_ending/verzeichnis.php?l=$letter_small\" title=\"$letter_big $this->display_cat ".L_DOWNLOADS."\">$letter_big</a>";
				else
					$content .= " &nbsp;&nbsp;";
			}
		}
		$content = substr($content, 1);
		return $content;
		*/
	}
	
	function GetPages()
	{
		global $maxentries_directory;
		$pages = (int)($this->total_results/$maxentries_directory);
		if ($this->total_results % $maxentries_directory != 0)
			$pages++;
		$content = '';
		if ($pages <= 1)
			$content .= ' 1';
		else
		{
			global $skip_pages_directory;
			global $domain_ending;
			$first = $this->page - $skip_pages_directory > 1 ? $this->page - $skip_pages_directory : 1;
			$letter_small = strtolower($this->letter);
			if ($first != $this->page)
				$content .= " <a href=\"http://$this->category.zoozle.$domain_ending/verzeichnis.php?l=$letter_small&p=".($this->page-1)."$this->order_link\" title=\"$this->letter $this->display_cat ".L_DOWNLOADS." ".L_PREVIOUS."\">&lt;</a>";
			for ($i = $first; $i < $this->page; $i++)
				$content .= " <a href=\"http://$this->category.zoozle.$domain_ending/verzeichnis.php?l=$letter_small&p=$i$this->order_link\" title=\"$this->letter $this->display_cat ".L_DOWNLOADS." ".L_PAGE." $i\">$i</a>";
			$content .= " $this->page";
			$last = $this->page + $skip_pages_directory < $pages ? $this->page + $skip_pages_directory : $pages;
			for ($i = $this->page + 1; $i <= $last; $i++)
				$content .= " <a href=\"http://$this->category.zoozle.$domain_ending/verzeichnis.php?l=$letter_small&p=$i$this->order_link\" title=\"$this->letter $this->display_cat ".L_DOWNLOADS." ".L_PAGE." $i\">$i</a>";
			if ($last != $this->page)
				$content .= " <a href=\"http://$this->category.zoozle.$domain_ending/verzeichnis.php?l=$letter_small&p=".($this->page+1)."$this->order_link\" title=\"$this->letter $this->display_cat ".L_DOWNLOADS." ".L_NEXT."\">&gt;</a>";
		}
		$content = substr($content, 1);
		$content = L_PAGE . ': <div class="strong">' . $content . '</div>';
		return $content;
	}
	
	function GetOtherCategories()
	{
		global $search_category;
		global $domain_ending;
		$letter_small = strtolower($this->letter);
		$content = '';
		foreach ($search_category as $index => $category)
		{
			if ($index == $this->category || $index == DOMAIN_USENET)
				continue;
			$content .= " <a href=\"http://$index.zoozle.$domain_ending/verzeichnis.php?l=$letter_small\" title=\"$this->letter $category ".L_DOWNLOADS."\">$category</a>\r\n";
		}
		$content = substr($content, 1);
		return $content;
	}
	
	function GetEntries()
	{
		global $page_lang;
		global $maxentries_directory;
		global $domain_ending;
		
		$query = "SELECT COUNT(DISTINCT name) FROM $this->category"."_$page_lang WHERE name LIKE '$this->letter%'";
		/*
		$query = "CREATE TEMPORARY TABLE letter_entries (SELECT COUNT(*) AS entries FROM $this->category"."_$page_lang WHERE name LIKE '$this->letter%' GROUP BY name);";
		mysql_query($query);
		$query = "SELECT SUM(entries) FROM letter_entries;";
		*/
		$result = mysql_query($query);
		$this->total_results = mysql_fetch_row($result);
		$this->total_results = $this->total_results[0];
		
		$start = ($this->page-1)*$maxentries_directory;
		//$query = "SELECT name, name_short FROM $this->category"."_$page_lang WHERE name LIKE '$this->letter%' GROUP BY name ORDER BY $this->order_sql LIMIT $start, $maxentries_directory";
		$query = "SELECT name, name_short FROM names_$this->category"."_$page_lang WHERE name LIKE '$this->letter%' ORDER BY $this->order_sql LIMIT $start, $maxentries_directory";
		$result = mysql_query($query);
		$content = '';
		while ($row = mysql_fetch_object($result))
		{
			$link = urlencode(strtolower($row->name_short));
			$content .= " <a href=\"http://$this->category.zoozle.$domain_ending/download.php?n=$link\" title=\"$row->name\">$row->name</a><br />\r\n";
		}
		$content = substr($content, 1);
		return $content;
	}
};
?>
