<?php
include_once 'DbToSpreadSheet.php';

global $spreadSheetOps;

if(isset($_GET['authenticate']))
{
	if($_GET['authenticate'] == 1)
	{
		if(isset($_GET['email']) && isset($_GET['pass']))
		{
			$email = $_GET['email'];
			$password = $_GET['pass'];
			
			$spreadSheetOps = new SpreadSheetOps($email,$password);
			if($spreadSheetOps->authenticate())
			{
				echo $spreadSheetOps->getToken();
			}
			else
			{
				echo 0;
			}
		}
	}
}
else if(isset($_GET['getSpreadSheets']))
{
	if($_GET['getSpreadSheets'] == 1)
	{
		if(isset($_GET['email']) && isset($_GET['pass']))
		{
			$email = $_GET['email'];
			$password = $_GET['pass'];
			
			$spreadSheetOps = new SpreadSheetOps($email,$password);
			$spreadSheetOps->authenticate();
			$myTestArray = array();
			$myTestArray[] = $spreadSheetOps->getSpreadSheetNames();
			
			for($i=0 ; $i< count($myTestArray[0]) ; $i++)
				echo $myTestArray[0][$i] . ",";
		}
	}
}
else if(isset($_GET['getWorkSheets']))
{
	if($_GET['getWorkSheets'] == 1)
	{
		if(isset($_GET['email']) && isset($_GET['pass']) && isset($_GET['sheetName']))
		{
			$email = $_GET['email'];
			$password = $_GET['pass'];
			$spreadSheetName = $_GET['sheetName'];
			
			$spreadSheetOps = new SpreadSheetOps($email,$password);
			$spreadSheetOps->authenticate();
			$spreadSheetOps->setSpreadSheetId($spreadSheetName);
			$myTestArray = array();
			$myTestArray[] = $spreadSheetOps->getWorkSheetNames();
			
			for($i=0 ; $i< count($myTestArray[0]) ; $i++)
				echo $myTestArray[0][$i] . ",";
		}
	}
}
else if(isset($_GET['getSpreadSheetId']))
{
	if($_GET['getSpreadSheetId'] == 1)
	{
		if(isset($_GET['email']) && isset($_GET['pass']) && isset($_GET['spName']))
		{
			$email = $_GET['email'];
			$password = $_GET['pass'];
			$spreadSheetName = $_GET['spName'];
			
			$spreadSheetOps = new SpreadSheetOps($email,$password);
			$spreadSheetOps->authenticate();
			echo $spreadSheetOps->setSpreadSheetId($spreadSheetName);
		}
	}
}
else if(isset($_GET['dumpDb']))
{
	if($_GET['dumpDb'] == 1)
	{
		if(isset($_GET['email']) && isset($_GET['pass']) && isset($_GET['spName']) && isset($_GET['wsName']) &&
			isset($_GET['host']) && isset($_GET['dbUser']) && isset($_GET['dbName']) && isset($_GET['dbPass']) &&
			isset($_GET['dbQuery']))
		{
			$host		= $_GET['host'];
			$dbUser		= $_GET['dbUser'];
			$dbPassword = $_GET['dbPass'];
			$dbName		= $_GET['dbName'];
			$query		= $_GET['dbQuery'];
			$email		= $_GET['email'];
			$gmailPass	= $_GET['pass'];
			$spreadSheet= $_GET['spName'];
			$workSheet	= $_GET['wsName'];
	
			$dbToSpreadSheetObj = new DbToSpreadSheet($host,$dbUser,$dbPassword,$dbName,$email,$gmailPass,$spreadSheet,$workSheet);
			echo $dbToSpreadSheetObj->dumpDbToSpreadSheet($query);
		}
	}
}
else if(isset($_GET['test'])){
	if($_GET['test'] == 1){
		$email = $_GET['email'];
		$password = $_GET['pass'];
			
		$spreadSheetOps = new SpreadSheetOps($email,$password);
		$spreadSheetOps->authenticate();
		$token = $spreadSheetOps->getToken();
		
		$spreadSheetOps->getSpreadSheetNames($token);
	}
}
?>