<h1>Schedules overzicht</h1>
<?php
function PrintArrayBasic($item, $key)
{
	global $_cfg;

	if ($item == false)
	{
		$item = "false";
	}
	if (is_array($item))
	{
		//Key is het schedule nummer. Extra info ophalen
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/schedules/".$key;
		$Schedule	= GetJSON($Url);

		
		echo "    <tr>\n";
		echo "      <td style=\"text-align: left;\">".$Schedule["name"]."</td>\n";
		echo "      <td style=\"text-align: left;\">".$Schedule["description"]."</td>\n";
		echo "      <td style=\"text-align: left;\">".$Schedule["command"]["address"]."</td>\n";
		echo "      <td style=\"text-align: left;\">".$Schedule["time"]."</td>\n";
		echo "    </tr>\n";
	}
}

$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/schedules";
$Config		= GetJSON($Url);

?>
                <h2>Alle Schedules</h2>
<table>
  <thead>
    <tr>
      <th>Naam</th>
      <th>description</th>
      <th>address</th>
      <th>time</th>
    </tr>
  </thead>
  <tbody>
<?php
array_walk($Config,"PrintArrayBasic");
?>
  </tbody>
</table>
