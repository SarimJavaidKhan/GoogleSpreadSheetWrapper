<?php
// default timezone set
//date_default_timezone_set("America/Dawson_Creek");

include_once 'logging.php';

//Google Api Urls Settings
define("GMAIL_ID","gmailid@gmail.com");
define("GMAIL_PASSWORD","gmailpass");
define("SPREADSHEET","PhpSheet");
define("WORKSHEET","Sheet3");

define("AUTH_URL","https://www.google.com/accounts/ClientLogin");
define("SPREADSHEETS_URL","https://spreadsheets.google.com/feeds/spreadsheets/private/full?title=");
define("WORKSHEETS_URL","https://spreadsheets.google.com/feeds/worksheets/");
define("CELLS_DATA_URL","https://spreadsheets.google.com/feeds/cells/");
define("ADD_DATA_ROW_URL","https://spreadsheets.google.com/feeds/list/");
define("ALL_SS_URL","https://spreadsheets.google.com/feeds/spreadsheets/private/full");

//URLs for end users
define("BROWSER_URL_COMMON", "/spreadsheets/d/" );

//logging settings
if (!defined("LOGFILE")){ 
	define("LOGFILE", dirname(__FILE__)."/script-log-". date('Y-m-d', time()) .".txt");
}

//Database Settings
define("SERVER", "localhost");	
define("DATABASE", "sarimdb");
define("USER", "sarim1");
define("PASSWORD", "root");
define("DEFAULT_QUERY","select * from whatever");
?>