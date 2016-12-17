<?php
/**********************************************************************************
* Settings.php                                                                    *
***********************************************************************************
* SMF: Simple Machines Forum                                                      *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                    *
* =============================================================================== *
* Software Version:           SMF 1.1                                             *
* Software by:                Simple Machines (http://www.simplemachines.org)     *
* Copyright 2006 by:          Simple Machines LLC (http://www.simplemachines.org) *
*           2001-2006 by:     Lewis Media (http://www.lewismedia.com)             *
* Support, News, Updates at:  http://www.simplemachines.org                       *
***********************************************************************************
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the provided license as published by Simple Machines LLC.          *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
* See the "license.txt" file for details of the Simple Machines license.          *
* The latest version can always be found at http://www.simplemachines.org.        *
**********************************************************************************/


########## Maintenance ##########
# Note: If $maintenance is set to 2, the forum will be unusable!  Change it to 0 to fix it.
$mtitle = 'Maintenance Mode';		# Title for the Maintenance Mode message.
$mmessage = 'Okay faithful users...we\'re attempting to restore an older backup of the database...news will be posted once we\'re back!';		# Description of why the forum is in maintenance mode.

########## Forum Info ##########
$mbname = 'Filesharing-Forum & FAQ - Azureus, uTorrent, BitTorrent, eMule';		# The name of your forum.
$language = 'german';		# The default language file set for the forum.
$boardurl = 'http://chungo.net/forum';		# URL to your forum's folder. (without the trailing /!)
$webmaster_email = 'noreply@myserver.com';		# Email address to send emails from. (like noreply@yourdomain.com.)
$cookiename = 'SMFCookie62';		# Name of the cookie to set for authentication.

########## Database Info ##########
//Ekliptor> use local database
//$db_server = '192.168.39.1';
$db_server = 'localhost';
//Ekliptor< use local database
$db_name = 'chungo_forum';
$db_user = 'root';
$db_passwd = 'rouTer99';
$db_prefix = 'smf_';
$db_persist = 0;
$db_error_send = 1;

########## Directories/Files ##########
# Note: These directories do not have to be changed unless you move things.
$boarddir = '/server/wwwroot/chungo_net/forum';		# The absolute path to the forum's folder. (not just '.'!)
$sourcedir = '/server/wwwroot/chungo_net/forum/Sources';		# Path to the Sources directory.

########## Error-Catching ##########
# Note: You shouldn't touch these settings.
$db_last_error = 1244554939;


# Make sure the paths are correct... at least try to fix them.
if (!file_exists($boarddir) && file_exists(dirname(__FILE__) . '/agreement.txt'))
	$boarddir = dirname(__FILE__);
if (!file_exists($sourcedir) && file_exists($boarddir . '/Sources'))
	$sourcedir = $boarddir . '/Sources';

$db_character_set = 'utf8';
?>
