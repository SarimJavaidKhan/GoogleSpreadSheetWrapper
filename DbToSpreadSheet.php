<?php
include_once 'GoogleSpreadSheetAPI.php';

/*
Methods:-
	*Constructor()
	*connectToDb()
	*closeDb()
	*dumpDbToSpreadSheet()
*/
class DbToSpreadSheet
{
	private $link;
	private $dbObject;
	private $spreadSheetOps;
	
	public function __construct($server,$user,$dbPassword,$dbName,$gmailId,$gmailPassword,$spreadSheetName,$workSheetName)
	{
		if($server == null)
			$server = SERVER;
		if($user == null)
			$user = USER;
		if($dbPassword == null)
			$dbPassword = PASSWORD;
		if($dbName == null)
			$dbName = DATABASE;
			
		$this->dbName = $dbName;
		$this->spreadSheetOps = new SpreadSheetOps($gmailId,$gmailPassword);
		$this->spreadSheetOps->init($spreadSheetName,$workSheetName);
		$this->connectToDb($server,$user,$dbPassword,$dbName);
	}
	
	//Starting sql connection and selecting the db for the current link to be used.
	public function connectToDb($server,$user,$password,$dbName)
	{	
		$this->link = mysqli_connect($server,$user,$password,$dbName);
		if(!$this->link)
		{
			WriteError("DbToSpreadSheet.connectToDb()-> Could not connect to the server");
			die("Could not connect to the server!" . mysqli_error($this->link));
		}
		else
			WriteInfo("DbToSpreadSheet.connectToDb()->Connected to server");
			
		$this->dbObject = mysqli_select_db($this->link,$dbName);
		if(!$this->dbObject)
		{
			WriteError("DbToSpreadSheet.connectToDb()-> Could not connect to the database");
			echo "Could not connect to the server";
		}
		else
		{
			WriteInfo("DbToSpreadSheet.connectToDb()->Connected to DATABASE");
		}
	}
	
	public function closeDb()
	{
		mysqli_close($this->link);
	}
	
	public function dumpDbToSpreadSheet($query)
	{
		if($query == null || empty($query))
			$query = DEFAULT_QUERY;
		
		$tableDataResultSet = mysqli_query($this->link,$query);
		if(!$tableDataResultSet)
		{
			WriteError("DbToSpreadSheet.dumpDbToSpreadSheet()->Some issue in the query");
			die(mysqli_error($this->link));
		}
		else
		{
			WriteInfo("DbToSpreadSheet.dumpDbToSpreadSheet->Query executed successfully.");
		}
		
		//Getting headers of the result fetched from the query
		$headerDbRow = mysqli_fetch_assoc($tableDataResultSet);
		$headerRow = array();
		$i=0;
		while($i != count($headerDbRow)){
			$headerRow[] = key($headerDbRow);
			next($headerDbRow);
			$i++;
		}
		//Pushing the headers to the spreadsheet
		$this->spreadSheetOps->addHeaderRow($headerRow);
		
		mysqli_data_seek($tableDataResultSet, 0);
		
		//Getting further data from the query and dumping to spreadsheet.
		while($row = mysqli_fetch_array($tableDataResultSet , MYSQL_ASSOC)){
			$this->spreadSheetOps->addRow($row);
		}
		
		$this->closeDb();
		return $this->spreadSheetOps->getUpdatedSheetUrl();
	}
}

?>