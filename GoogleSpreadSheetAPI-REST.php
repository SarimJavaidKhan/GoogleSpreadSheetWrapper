<?php
include_once 'GoogleSpreadSheetAPI.php';

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
			$token = $spreadSheetOps->getToken();
			$myTestArray = array();
			$myTestArray[] = $spreadSheetOps->getSpreadSheetNames($token);
			
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