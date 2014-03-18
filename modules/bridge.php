<h1>Bridge settings </h1>
<script>
function DeleteUser(user)
{
	var output=confirm("Gebruiker wissen?");
	if (output==true)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=DeleteUser&user='+user,true);

		xmlhttp.onreadystatechange=function() 
		{
			 if (xmlhttp.readyState==4) 
			 {
				 ShowMessage(xmlhttp.responseText)
			 }
		}
		xmlhttp.send();

		//row ook removen, anders kloppen de kleuren niet meer.
		$("#Row_"+user).hide('slow', function()  { $("#Row_"+user).remove() });

	}
}


</script>


<?php
function PrintArrayBasic($item, $key)
{
	if ($item == false)
	{
		$item = "false";
	}
	if (!is_array($item))
	{
		echo "    <tr>\n";
		echo "      <td style=\"text-align: left;\">".$key."</td>\n";
		echo "      <td style=\"text-align: left;\">". $item."</td>\n";
		echo "    </tr>\n";
	}
}

function PrintArrayWhitelist($item, $key)
{
	if ($item == false)
	{
		$item = "false";
	}
	if (is_array($item))
	{		
		echo "    <tr id=\"Row_".$key."\">\n";
		echo "      <td style=\"text-align: left;\">". $item["name"]."</td>\n";
		echo "      <td style=\"text-align: left;\">". $item["create date"]."</td>\n";
		if ((time() - strtotime($item["last use date"])) > ( 86400 * 30) ) //30 dagen
		{
			echo "      <td style=\"text-align: left;\"><span style=\"color:#F00; font-weight:bold; float:left\" title=\"Niet gebruikt in ".round((time() -strtotime($item["last use date"]))/86400) ." dagen.\">". $item["last use date"]."</span><span style=\"color:#F00; font-weight:bold; float:right\"><img src=\"images/del.png\" style=\"cursor:pointer\" onclick=\"DeleteUser('".$key."');\"/></span></td>\n";
		}
		else
		{
			echo "      <td style=\"text-align: left;\"><span style=\"float:left\">". $item["last use date"]."</span><span style=\"color:#F00; font-weight:bold; float:right\"><img src=\"images/del.png\" style=\"cursor:pointer\" onclick=\"DeleteUser('".$key."');\"/></span></td>\n";
		}
		echo "      <td style=\"text-align: left;\">".$key."</td>\n";
		echo "    </tr>\n";
	}
}

$Config = GetJSON($_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/config");

?>
<h2>Algemene settings</h2>
<table>
  <thead>
    <tr>
      <th>Setting</th>
      <th>Value</th>
    </tr>
  </thead>
  <tbody>
<?php
array_walk($Config,"PrintArrayBasic");
?>
  </tbody>
</table>


<h2>Whitelist settings</h2>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Create date</th>
      <th>Last use date</th>
      <th>HASH</th>
    </tr>
  </thead>
  <tbody>
<?php
array_walk($Config["whitelist"],"PrintArrayWhitelist");
?>
  </tbody>
</table>

<h2>Software update</h2>
<table>
  <thead>
    <tr>
      <th>Setting</th>
      <th>Value</th>
    </tr>
  </thead>
  <tbody>
<?php
array_walk($Config["swupdate"],"PrintArrayBasic");
?>
  </tbody>
</table>