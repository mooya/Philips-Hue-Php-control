<?php

session_start(); 

include 'includes/sql_settings.php'; 
include 'includes/settings.php'; 
include 'includes/functions.php'; 

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['Username']) && isset($_POST['Password']) ) 
{ 
	if (strtolower($_cfg["Login"]['Username'] )  == strtolower($_POST['Username'] ) && $_cfg["Login"]['Password']  == $_POST['Password']  && $_cfg["Login"]['Username'] <> ""  && $_cfg["Login"]['Password'] <> "" )
	{
		$_SESSION['LoginCheck'] = 1;
		header("Location: index.php"); 
		exit;	
	}
	else
	{
		$errorcode =1 ;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Hue</title>
<base href="<?= $_cfg["WebsiteURL"] ?>" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="stylesheet" href="style.css">

</head>

<body>
<form name="inlogform" method="post" action="">
              <table style="width:500px;"  border="0" align="center" cellpadding="2" cellspacing="0">
                  <tr>
                    <td colspan="3" style="color:#FFF; background-color:#0079C8; text-transform:uppercase;" align="center"><strong>Inloggen</strong></td>
                  </tr>
                  <tr>
                    <td width="150" rowspan="4">&nbsp;</td>
                    <td style="color:#FF0000" colspan="2"><?php if (isset($errorcode) && $errorcode != 0) { echo "<strong>Foute login gegevens</strong>"; } ?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="150">Gebruikersnaam</td>
                    <td width="150"><INPUT NAME="Username" id="Username" tabindex="1" TYPE="text" value="" SIZE="15" maxlength="25" style="width:125px"></td>
                  </tr>
                  <tr>
                    <td>Wachtwoord</td>
                    <td><INPUT NAME="Password" id="Password" tabindex="2" TYPE="password" size="15" maxlength="32" style="width:125px"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="Submit" value="Inloggen" style="width:125px"/></td>
                  </tr>

                </table>
</form>
</body>
</html>