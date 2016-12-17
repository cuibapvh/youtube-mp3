Cache-Control: max-age=1753032704
Expires: Tue, 29 Dec 2065 16:16:42 GMT

<?php
// ** MySQL settings ** //
define('WP_CACHE', true); //Added by WP-Cache Manager
define('DB_NAME', 'chungo');    // The name of the database
define('DB_USER', 'root');     // Your MySQL username
define('DB_PASSWORD', 'rouTer99'); // ...and password
define('DB_HOST', 'localhost'); 
#define('DB_HOST', '192.168.39.1');

/* Die datei hier hat nur auswirkungen auf die startseiten und die links dort */

/*
$load = sys_getloadavg();
if ( $load[0] < 19 ){
	define('DB_HOST', 'localhost');    // 99% chance you won't need to change this value
} elseif ( $load[0] > 19 ) {
	#define('DB_HOST', '77.247.178.21');
	define('DB_HOST', '192.168.39.1'); 
};
*/

// Change each KEY to a different unique phrase.  You won't have to remember the phrases later,
// so make them long and complicated.  You can visit http://api.wordpress.org/secret-key/1.1/
// to get keys generated for you, or just make something up.  Each key should have a different phrase.
define('AUTH_KEY', '$ANi>:cs8)2khh*8HH<u8bu}Swc>t&W *EmNbc-QwSh+b|PX*<f\\f*/\"~(TaO&nf');
define('SECURE_AUTH_KEY', '@t1By;O:5QiMGHZy2R&R K}sP?fg.jep\").rgE_oBx2^fv2]x\"#)7tGX[bu31s{J');
define('LOGGED_IN_KEY', '*Sx.~7Owg?i\';cCurhb+i&I<$gxGj}`*#x%A@C34JuNG}<|zcQ!g EI,K\'R_xVGE');

// You can have multiple installations in one database if you give each a unique prefix
$table_prefix  = 'wp_';   // Only numbers, letters, and underscores please!

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
define ('WPLANG', '');

/* That's all, stop editing! Happy blogging. */

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
?>