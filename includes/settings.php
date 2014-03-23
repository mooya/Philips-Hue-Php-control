<?php
date_default_timezone_set('Europe/Amsterdam');

$_cfg["WebsiteURL"] 					= "http://192.168.0.1/hue/";//Url van het script   (of zet hier alleen /hue/ neer)
$_cfg["BridgeIP"] 						= "192.168.0.100";//Ip adres Hue bridge
$_cfg["UseMysql"] 						= true;//extra opties zoals werken met eigen groepen. true or false
$_cfg["BridgeHash"] 					=  "a972dacd094fd40fe78cbe95451197dc98a1cdc9";//random hash, maak op bijv: http://www.sha1-online.com/
$_cfg["CheckHash"] 					=  true;//controle of de hash geldig is, true or false. Kan op false indien alles goed werkt.
$_cfg["ModRewrite"] 					=  false;//Apache Mod Rewrite inschakelen? Zet dit op False indien je geen mod-rewrite wilt gebruiken (op bijv. IIS)
$_cfg["Timezone"]["Latiture"] 		=  52.509535;//nodig voor bepalen sunset + sunrise
$_cfg["Timezone"]["Longitude"] 	=  5.26062;
$_cfg["Timezone"]["Offzet"] 		= 1;
$_cfg["HideLights"] 						= array();//lamp nummers die verborgen mogen worden. (worden wel weergegeven indien ze in een groep zitten en je selecteerd die groep) (bijv: array(1,5)
$_cfg["HideNonHueLights"] 		= 1; //lampen verbergen die geen kleur kunnen veranderen in het 'dragable' overzicht

/***
 * Waar moet het begin van het IP adres van de bezoeker mee matchen? 
 *  Leeglaten indien elk IP de pagina mag zien
 *  Je kan een deel van het IP adres invullen, om zo een intern netwerk toegang te geven														
 */
$_cfg["Login"]['IPMatch'] 			= "192.168.0."; 														 


/***
 * Gebruikersnaam en Password?
 * Vul hier een username en password in om het loginscherm te activeren.
 * Indien de username leeg blijft wordt er dus geen loginscherm weergegeven
 * Indien de IPMatch hierboven en de Username hier onder beide leeg zijn heeft iedereen toegang
 * Indien de IPMarch en Username beide zijn ingevuld wordt het loginscherm alleen weergegeven indien het IP adres niet matched.
 */
$_cfg["Login"]['Username'] 			= "";//niet case sensitive
$_cfg["Login"]['Password'] 			= "";//wel case sensitive



if ($_cfg["UseMysql"] )
{
	$DB = mysqli_connect($Host,$User,$Password,$Database) or die("Database Connect Error");
}


error_reporting( E_ALL );
ini_set('display_errors','On');


?>