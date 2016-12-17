<?php
$server   = "localhost";
$database = "youtube";
$username = "root";
$password = "rouTer99";

$mysqlConnection = mysql_connect($server, $username, $password);
if (!$mysqlConnection)
{
  echo "offline";
}
else
{
echo "online";
//mysql_select_db($database, $mysqlConnection);
}
mysql_close($mysqlConnection);
exit(0);
?>