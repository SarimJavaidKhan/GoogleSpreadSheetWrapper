<?php
include_once(dirname(__FILE__)."/script-config.php");
function WriteError($message)
{
	$loggedString = PHP_EOL . "[" . date('Y-m-d h:i:s a', time()) . "][ERROR]: " . $message;
	WriteLog($loggedString);
	return $loggedString;
}
function WriteInfo($message)
{
	$loggedString = PHP_EOL . "[" . date('Y-m-d h:i:s a', time()). "][INFO]: " . $message;
	WriteLog($loggedString);
	return $loggedString;
}
function WriteLog($loggedString)
{
	file_put_contents(LOGFILE, $loggedString, FILE_APPEND | LOCK_EX);
	chmod(LOGFILE, 0777);
}
?>