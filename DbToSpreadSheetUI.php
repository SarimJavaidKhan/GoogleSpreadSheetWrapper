<?php
include_once 'Main.php';

	if(isset($_POST["submitBtn"]))
	{
		$host		= $_POST['hostTxt'];
		$dbUser		= $_POST['dbUserTxt'];
		$dbPassword = $_POST['dbPassTxt'];
		$dbName		= $_POST['dbNameTxt'];
		$query		= $_POST['queryTxt'];
		$email		= $_POST['gmailIdTxt'];
		$gmailPass	= $_POST['gmailPassTxt'];
		$spreadSheet= $_POST['spreadSheetNameTxt'];
		$workSheet	= $_POST['workSheetNameTxt'];
		$query		= $_POST['queryTxt'];
		
		//you can call this method with null values. For null variables default values are set in scripts-config.php
		echo "Worksheet Updated Url: " . Main::DbToGoogleSpreadSheet($host,$dbUser,$dbPassword,$dbName,$email,$gmailPass,$spreadSheet,$workSheet,$query);
		
		unset($_POST['submitBtn']);	
	}
?>

<html>
<body>
<head>
<h1> MySQL DataBase To Google SpreadSheets </h1>
</head>

<form name='submitFrm' id='submitFrm' action='DbToSpreadSheetUI.php' method="POST">
<table border='0'>
<tr>
<th>---</th>
<th>---</th>
</tr>
<tr>
<td>Host:</td>
<td><input type='text' id='hostTxt' name='hostTxt'/> </td>
</tr>
<tr>
<td>Database User:</td>
<td><input type='text' id='dbUserTxt' name='dbUserTxt'/>  </td>
</tr>
<tr>
<td>Database Password:</td>
<td><input type='text' id='dbPassTxt' name='dbPassTxt'/>  </td>
</tr>
<tr>
<td>Database Name:</td>
<td><input type='text' id='dbNameTxt' name='dbNameTxt'/>  </td>
</tr>
<tr>
<td>Gmail Id:</td>
<td><input type='text' id='gmailIdTxt' name='gmailIdTxt'/>	</td>
</tr>
<tr>
<td>Gmail Password:</td>
<td><input type='Password' id='gmailPassTxt' name='gmailPassTxt'/>	</td>
</tr>
<tr>
<td>SpreadSheet Name:</td>
<td><input type='text' id='spreadSheetNameTxt' name='spreadSheetNameTxt'/> </td>
</tr>
<tr>
<td>Worksheet Name:</td>
<td><input type='text' id='workSheetNameTxt' name='workSheetNameTxt'/>	</td>
</tr>
<tr>
<td>Query:</td>
<td><textarea rows='10' cols='100' type='text' id='queryTxt' name='queryTxt'></textarea>  </td>
</tr>
<tr>
<td>
<input type='submit' id='submitBtn' name='submitBtn' value='Start Dumping'/>
<td>
</tr>
</table>
</form>

</body>
</html>

