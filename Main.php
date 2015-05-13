<?php
include_once 'DbToSpreadSheet.php';

class Main
{
	public static function DbToGoogleSpreadSheet($host,$dbUser,$dbPassword,$dbName,$email,$gmailPass,$spreadSheet,$workSheet,$query)
	{
		$dbToSpreadSheetObj = new DbToSpreadSheet($host,$dbUser,$dbPassword,$dbName,$email,$gmailPass,$spreadSheet,$workSheet);
		return $dbToSpreadSheetObj->dumpDbToSpreadSheet($query);
		
		//The other way, passing nulls
		//$dbToSpreadSheetObj = new DbToSpreadSheet(null,null,null,null,null,null,null,null);
		//$dbToSpreadSheetObj->dumpDbToSpreadSheet(null);
	}
	
	/*******
	Arguments:
	-host 		[Host]
	-dbuser		[DATABASEUSER]
	-dbpass		[DATABASEPASSWORD]
	-dbname		[DATABASENAME]
	-email		[EMAIL]
	-mailpass	[EMAILPASSWORD]
	-ss			[SPREADSHEET]
	-ws			[WORKSHEET]
	-query 		[QUERY]
	*******/
	
	/***
		Returned Value: Updated Worksheet url
	***/
	public static function execute($args)
	{
		$host 			= null;
		$dbUser 		= null;
		$dbPassword 	= null;
		$dbName			= null;
		$email			= null;
		$gmailPass		= null;
		$spreadSheet	= null;
		$workSheet		= null;	
		$query 			= null;
			
		$url = null;
		try
		{
			echo WriteInfo('Starting at ' . date('m/d/Y h:i:s a', time()) . "\n");
			echo "Dumping...\n";
			$argsSize = count($args);
			for($i=1 ; $i<$argsSize ; $i=$i+2)
			{
				if(strpos($args[$i],"-") == 0)
				{
						if($args[$i] == "-host"){
							if(strpos($args[$i+1],"-") !== 0) $host 		= $args[$i+1];
						}
						
						if($args[$i] == "-dbuser"){
							if(strpos($args[$i+1],"-") !== 0) $dbUser 		= $args[$i+1];
						} 		
						
						if($args[$i] == "-dbpass"){
							if(strpos($args[$i+1],"-") !== 0) $dbPassword 	= $args[$i+1];
						} 	
						
						if($args[$i] == "-dbname"){
							if(strpos($args[$i+1],"-") !== 0) $dbName 		= $args[$i+1];
						}
						
						if($args[$i] == "-email"){
							if(strpos($args[$i+1],"-") !== 0) $email		= $args[$i+1];
						} 		
						
						if($args[$i] == "-mailpass"){
							if(strpos($args[$i+1],"-") !== 0) $gmailPass 	= $args[$i+1];
						}	
						
						if($args[$i] == "-ss"){
							if(strpos($args[$i+1],"-") !== 0) $spreadSheet	= $args[$i+1];
						}
							
						if($args[$i] == "-ws"){
							if(strpos($args[$i+1],"-") !== 0) $workSheet	= $args[$i+1];
						}
						
						if($args[$i] == "-query"){
							if(strpos($args[$i+1],"-") !== 0) $query	= $args[$i+1];
						}
				}
				
			}
			
			$url = Main::DbToGoogleSpreadSheet($host,$dbUser,$dbPassword,$dbName,$email,$gmailPass,$spreadSheet,$workSheet,$query);
			
			echo WriteInfo("Worksheet Updated: ". $url);
			echo WriteInfo('Ending at ' . date('m/d/Y h:i:s a', time()) . "\n");			
		}
		catch(Exception $e)
		{
			echo WriteError($e->getMessage());
		}
		return $url;
	}
}
if(isset($argv))
	Main::execute($argv);

?>