<?php

// place this code inside a php file and call it f.e. "download.php"
$fullPath = "/home/wwwyoutube/app/android/MusicStar.apk";

if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        break;
        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"MusicStar.apk\"");
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
	ob_clean();
	flush();
	readfile($fullPath);
	exit(0);
}
// example: place this kind of link into the document where the file download is offered:
// <a href="download.php?download_file=some_file.pdf">Download here</a>
?>