<?php

require_once('/home/wwwyoutube/lib/getid3/getid3.php');


$songtext = <<<END
(Instrumental)

Tsunami, drop!

(Instrumental)
Drop, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami

Tsunami, drop!

Nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, nami na nami na nami na nami, drop!

(Instrumental)
END;

$getID3 = new getID3;
getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);
$tagwriter = new getid3_writetags;

$tagwriter->filename       = '/home/wwwyoutube/convert/test.mp3';
$tagwriter->overwrite_tags = false;
$tagwriter->tag_encoding   = "UTF-8";
$TagData['id3v2']['unsynchronised_lyrics'] = $songtext;
$tagwriter->tag_data = $TagData;

if ($tagwriter->WriteTags()) {
	echo 'Successfully wrote tags<BR>';
} else {
	echo "Failed to write tags! $tagwriter->errors";
}

var_dump($tagwriter);


?>