<?php
$email = $_REQUEST['email'];
if (strpos($email,'@') !== false) {
    echo "$email hat am Gewinnspiel teilgenommen!";
} else {
	echo "Email $email nicht bekannt.";
}
exit(0);
?>