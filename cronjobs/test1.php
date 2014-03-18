<?php
/*
 * Voorbeeld script voor cronjob
 * Stel hier welke lamp aan moet X aantal minuten voor / of na zons-ondergang
 * Laat deze pagina bijv. elke minuut openen dmv een cronjob.
 * Open de pagina dan als: http://{IP}/cronjobs/test1.php?key=8ab31432db6fd5fee11f86cf17d6d646c08433b6
 * De key is een eigen 'password' welke je hier onder opgeeft.
 */

session_start(); 
$sid = session_id();

include '../includes/sql_settings.php'; 
include '../includes/settings.php'; 
include '../includes/functions.php'; 


$Light			= 2;//Lamp nummer welke aan moet.
$LightsOn	= -25;  //Wanneer moet de lamp aan. hoeveel MINUTEN VOOR of NA  de zons-ondergang. (dus negatief om lampen voor de tijd aan te zetten, en positief om ze na zons ondergang aan te doen)
$Repeat		= 10; //nog hoeveel minuten moet het signaal gestuurd worden om de lampen aan te doen.. Dus als ze tussen deze tijd worden uitgezet gaan ze automatisch weer aan.
$Key				= "8ab31432db6fd5fee11f86cf17d6d646c08433b6"; //secure hash welke meegegeven moet worden bij openen pagina. Kan een random iets zijn, of leeg. Roep de pagina dan aan: ***.php?key=xxx

if ($Key <> "" && (!isset($_GET["key"]) || $_GET["key"] <> $Key)  )
{
	die('Ongeldige key.');
}

$Date 			= date("Y-m-d");
$Timestamp = time();
$Sunrise		= Sunrise(time());
$Sunset		= Sunset(time());


echo "\n<br>date: ".$Date. " ". date("H:i:s");
echo "\n<br>sunrise: ".date("H:i:s",$Sunrise);
echo "\n<br>sunset: ".date("H:i:s",$Sunset);

if ($Sunset < time())
{
	$Tijd			=time() - $Sunset; 
	echo "\n <br> sunset was: ".sec2hms($Tijd)." uur geleden";	
}
else
{
	$Tijd			=$Sunset - time() ; 
	echo "\n <br> sunset is over: ".sec2hms($Tijd)." uur";
}


$LightsOnTimestamp		= $Sunset + ($LightsOn*60);
$LightsOnTillTimestamp	= $LightsOnTimestamp + ($Repeat*60);
echo "\n<br>Lampen aan doen om: ".date("H:i:s",$LightsOnTimestamp);
echo "\n<br>Lampen aan doen tot: ".date("H:i:s",$LightsOnTillTimestamp);

if (time()  > $LightsOnTimestamp && time()  < $LightsOnTillTimestamp)
{
	echo "\n<br><strong>Lampen mogen aan!<strong>";
	$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".$Light."/state";
	$Data			= "{\"on\":true}";
	PutJSON($Url,$Data);
}
else
{
	echo "\n<br>Lampen hoeven niet aan";
}

?>