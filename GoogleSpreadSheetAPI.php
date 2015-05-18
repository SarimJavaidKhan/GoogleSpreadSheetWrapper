<?php
/*
Methods:-
	*constructor(email,password,spreadsheetname,workSheetName)
	*authenticate() 											// gets unique token from google for a particular gmail and password.
	*setSpreadSheetName($spreadSheetName)						// sets spreadsheet name
	*setSpreadSheetId()											// sets spreadsheet's id for the final url hit.
	*setWorkSheetName($worksheetname)							// sets worksheet name
	*setWorkSheetId()											// sets worksheet's id for the final url hit.
	*getColumnIDs()												// names of columns of specified worksheet
	*addRow($rowToAddArray)										// adds a row having values for the matched keys with that of the worksheets column names.
	*formatColumnId($val)										// Trims some value
*/

include_once 'script-config.php';

class SpreadSheetOps
{
	private $email;
	private $password;
	
	private $token;
	private $spreadSheetName;
	private $spreadSheetId;
	private $workSheetName;
	private $workSheetId;
	
	public $worksheetUrl;
	
	public function __construct($email,$password)
	{
		if($email == null)
			$this->email = GMAIL_ID;
		else
			$this->email = $email;
			
		if($password == null)
			$this->password = GMAIL_PASSWORD;
		else
			$this->password = $password;
	}
	
	public function init($spreadSheetName,$workSheetName)
	{
		//setting authentication string to $this->token so that it is used for future.
		$this->authenticate($this->email,$this->password);
		
		if($spreadSheetName == null)
			$spreadSheetName = SPREADSHEET;
		if($workSheetName == null)
			$workSheetName = WORKSHEET;
			
		//setting spreadsheet for future use.
		$this->setSpreadSheetName($spreadSheetName);
		$this->setSpreadSheetId($spreadSheetName);
		
		//setting worksheet for future use.
		$this->setWorkSheetName($workSheetName);
		$this->setWorkSheetId($workSheetName);
	}
	
	public function authenticate()
	{
		$authUrl = AUTH_URL;
		$authFields = array
					("accountType => HOSTED_OR_GOOGLE",
					 "Email" => $this->email,
					 "Passwd" => $this->password,
					 "service" => "wise",
					 "source" => "pfbc"
		);

		$curl = curl_init($authUrl);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $authFields);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if($status == 200)
		{
			if(stripos($response, "auth=") !== false) {
				preg_match("/auth=([a-z0-9_\-]+)/i", $response, $matches);
				$this->token = $matches[1];
				WriteInfo("Google Authentication success");
				return TRUE;
			}
		}
		else
		{
			WriteError("SpreadSheetOps.authenticate()-> Google authentication failed");
		}
		 return FALSE;
	}
	
	public function getToken()
	{
		if(isset($this->token))
			return $this->token;
		else
			return "you need to call authenticate() fist";
	}
	
	public function setSpreadSheetName($spreadSheetName)
	{
		$this->spreadSheetName = $spreadSheetName;
		//echo "SpreadSheetName: " . $spreadSheetName . "<br/>";
	}
	
	public function setSpreadSheetId($spreadSheetName)
	{
		$url = SPREADSHEETS_URL . urlencode($spreadSheetName);
		$headers = array(
							"Authorization: GoogleLogin auth=" . $this->token,
							"GData-Version: 3.0"
		);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		if($status == 200){
			$spreadSheetXML = simplexml_load_string($response);
			if($spreadSheetXML->entry){
				foreach($spreadSheetXML->entry as $entry){
					if($entry->title == $spreadSheetName){
						$this->spreadSheetId = basename(trim($entry->id));
						WriteInfo("SpreadSheetOps.setSpreadSheetId()->Unique Id of spreadsheet successfully retrieved");
						return $this->spreadSheetId;
					}
				}
			}
		}
		else{
			WriteError("SpreadSheetOps.setSpreadSheetId()->Spreadsheet Id retrieval failed");
		}
	}
	
	public function setWorkSheetName($workSheetName)
	{
		$this->workSheetName = $workSheetName;
		//echo "Worksheet Name: " . $workSheetName . "<br/>";
	}
	
	public function setWorkSheetId($workSheetName)
	{
		$url = WORKSHEETS_URL . $this->spreadSheetId . "/private/full?title=" . urlencode($workSheetName);
		$headers = array(
							"Authorization: GoogleLogin auth=" . $this->token,
							"GData-Version: 3.0"
		);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		if($status == 200)
		{
			$workSheetXML = simplexml_load_string($response);
			if($workSheetXML->entry)
			{
				$this->workSheetId = basename(trim($workSheetXML->entry[0]->id));
				WriteInfo("SpreadSheetOps.setWorkSheetId()->Unique Id of worksheet successfully retrieved");
				//echo "WorksheetId: " . $this->workSheetId . "<br/>";
				$this->getColumnIDs();
			}
		}
		else
		{
			WriteError("SpreadSheetOps.setSpreadSheetId()->Worksheet Id retrieval failed");
		}
	}
	
	public function getColumnIDs() 
	{
		$url = CELLS_DATA_URL . $this->spreadSheetId . "/" . $this->workSheetId . "/private/full?max-row=1";
		$headers = array(
			"Authorization: GoogleLogin auth=" . $this->token,
			"GData-Version: 3.0"
		);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if($status == 200) 
		{
			$columnIDs = array();
			$xml = simplexml_load_string($response);
			if($xml->entry) 
			{
				$columnSize = sizeof($xml->entry);
				for($c = 0; $c < $columnSize; ++$c)
					$columnIDs[] = $this->formatColumnID($xml->entry[$c]->content);
			}
			WriteInfo("SpreadSheetOps.getColumnIds->Column names of worksheet returned in an array");
			return $columnIDs;              
		}
		
		WriteError("SpreadSheetOps.getColumnIDs()->columnIds are empty");
	}
	
	public function getSpreadSheetNames()
	{
		$url = ALL_SS_URL;
		$headers = array(
			"Authorization: GoogleLogin auth=" . $this->token,
			"GData-Version: 3.0"
		);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		if($status == 200) 
		{
			$spreadSheetNames = array();
			$xml = simplexml_load_string($response);
			
			if($xml->entry) 
			{
				$columnSize = sizeof($xml->entry);
				//echo "Column Size" . $columnSize;
				for($c = 0; $c < $columnSize; ++$c)
					$spreadSheetNames[] = $xml->entry[$c]->title;
			}
			
			WriteInfo("SpreadSheetOps.getSpreadSheetNames->SpreadSheet names of an Id returned in an array");
			return $spreadSheetNames;
		}
	}
	
	public function getWorkSheetNames()
	{
		$url = WORKSHEETS_URL . $this->spreadSheetId . "/private/full";
		$headers = array(
			"Authorization: GoogleLogin auth=" . $this->token,
			"GData-Version: 3.0"
		);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		if($status == 200) 
		{
			$workSheetNames = array();
			$xml = simplexml_load_string($response);
			
			if($xml->entry) 
			{
				$columnSize = sizeof($xml->entry);
				//echo "Column Size" . $columnSize;
				for($c = 0; $c < $columnSize; ++$c)
					$workSheetNames[] = $xml->entry[$c]->title;
			}
			
			WriteInfo("SpreadSheetOps.getWorkSheetNames->WorkSheet names for a spreadsheetid");
			return $workSheetNames;
		}
	}
	
	public function addRow($data)
	{
		$url = ADD_DATA_ROW_URL . $this->spreadSheetId . "/" . $this->workSheetId . "/private/full";
		
		if(!empty($url)) 
		{
			$headers = array(
				"Content-Type: application/atom+xml",
				"Authorization: GoogleLogin auth=" . $this->token,
				"GData-Version: 3.0"
			);

			$columnIDs = $this->getColumnIDs();
			
			if($columnIDs) 
			{
				$fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">';
				foreach($data as $key => $value) {
					$key = $this->formatColumnID($key);
					if(in_array($key, $columnIDs))
						$fields .= "<gsx:$key><![CDATA[$value]]></gsx:$key>";
				}
				$fields .= '</entry>';

				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
				$response = curl_exec($curl);
				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);
				
				if($status == 200)
					WriteInfo("SpreadSheetOps.addRow()-> Row added successfully");
				else
					WriteError("SpreadSheetOps.addRow()-> Row not added successfully");
			}
		}
	}
	
	public function addHeaderRow($headerRow)
	{
		$url = CELLS_DATA_URL . $this->spreadSheetId . "/" . $this->workSheetId . "/private/full";
		if(!empty($url)) 
		{
			$headers = array(
				"Content-Type: application/atom+xml",
				"Authorization: GoogleLogin auth=" . $this->token,
				"GData-Version: 3.0"
			);

			$columnIDs = $this->getColumnIDs();
			$headerColsCount = count($headerRow);
			
			for($i=0 ; $i < $headerColsCount ; $i++ )
			{
				$fields = "";
				$fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">';
				$fields .= "<gs:cell row='1' col='" . ($i+1) . "' inputValue='$headerRow[$i]'>$headerRow[$i]</gs:cell>";
				$fields .= '</entry>';

				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
				$response = curl_exec($curl);
				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);
				
				if($status == 200)
					WriteInfo("SpreadSheetOps.addRow()-> Row added successfully");
				else
					WriteError("SpreadSheetOps.addRow()-> Row not added successfully");
			}
		}
	}
	
	public function getUpdatedSheetUrl()
	{	
		$url = WORKSHEETS_URL . $this->spreadSheetId . "/private/full?title=" . urlencode($this->workSheetName);
		$headers = array(
							"Authorization: GoogleLogin auth=" . $this->token,
							"GData-Version: 3.0"
		);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		if($status == 200)
		{
			$workSheetXML = simplexml_load_string($response);
			
			//echo $workSheetXML->link[0]['href'];
			if($workSheetXML->entry)
			{
				foreach($workSheetXML->entry[0]->link as $link){
					if($link['type'] == "text/csv"){
						$wsUrl = $link['href'];
						$leftSide = str_replace("/export","#",strtok($wsUrl,"?"));
						$rightSide = parse_str(str_replace("/export?","",substr($wsUrl,strpos($wsUrl,"/export?"))));
						
						$this->worksheetUrl = $leftSide . "gid=" . $gid;
						return $this->worksheetUrl;
					}
				}
				WriteInfo("SpreadSheetOps.getUpdatedSheetUrl()->Direct url to editable worksheet");
			}	
		}
		else
		{
			WriteError("SpreadSheetOps.setSpreadSheetId()->Worksheet Id retrieval failed");
		}
	}
	
	private function formatColumnID($val) 
	{
		return preg_replace("/[^a-zA-Z0-9.-]/", "", strtolower($val));
	}
}

?>