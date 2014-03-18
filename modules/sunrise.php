<h1>Tijden van zonsopkomst en -ondergang <?= date("Y")?></h1>
<p>Tijden voor <a href="https://maps.google.nl/maps?q=<?= $_cfg["Timezone"]["Latiture"]?>,<?= $_cfg["Timezone"]["Longitude"]?>&num=1&t=h&z=13" target="_blank"><?= $_cfg["Timezone"]["Latiture"]?>,<?= $_cfg["Timezone"]["Longitude"]?></a></p>
<table width="1000" border="1" id="SunriseTable">
  <tr>
    <th>&nbsp;</th>
    <td rowspan="33"></td>
    <th colspan="2">Jan</th>
    <td rowspan="33"></td>
    <th colspan="2">Feb</th>
    <td rowspan="33"></td>
    <th colspan="2">Mar</th>
    <td rowspan="33"></td>
    <th colspan="2">Apr</th>
    <td rowspan="33"></td>
    <th colspan="2">Mei</th>
    <td rowspan="33"></td>
    <th colspan="2">Jun</th>
    <td rowspan="33"></td>
    <th colspan="2">Jul</th>
    <td rowspan="33"></td>
    <th colspan="2">Aug</th>
    <td rowspan="33"></td>
    <th colspan="2">Sep</th>
    <td rowspan="33"></td>
    <th colspan="2">Okt</th>
    <td rowspan="33"></td>
    <th colspan="2">Nov</th>
    <td rowspan="33"></td>
    <th colspan="2">Dec</th>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
    <td align="center" style="font-weight:bold">Rise</td>
    <td align="center" style="font-weight:bold">Set</td>
  </tr>
<?php


 for($Day = 1; $Day  < 32; $Day++)
{
	echo "  <tr>\n";
	echo "    <td>". $Day ."</td>\n";
	for($Month = 1; $Month  < 13; $Month++)
	{
		if (checkdate($Month, $Day, date("Y")) <> false)
		{
			$Timestamp	= strtotime(date("Y")."-".$Month."-".$Day );
			$Sunrise		= date("H:i",Sunrise($Timestamp));
			$Sunset		= date("H:i",Sunset($Timestamp));
			
			
			$Class			= "";
			if (date("d-m-Y") == date("d-m-Y",$Timestamp) )
			{
				$Class		= " style=\"font-weight:bold; background-color:#007AC6; color:#fff\" ";
			}
			
			
			echo "    <td".$Class." onclick=\"alert('Datum ".date("d-m-Y",$Timestamp)."\\nZonsopkomst: ".$Sunrise."\\nZonsondergang: ".$Sunset."')\">".$Sunrise."</td>\n";
			echo "    <td".$Class." onclick=\"alert('Datum ".date("d-m-Y",$Timestamp)."\\nZonsopkomst: ".$Sunrise."\\nZonsondergang: ".$Sunset."')\">".$Sunset."</td>\n";
		}
		else
		{
			echo "    <td>&nbsp;</td>\n";
			echo "    <td>&nbsp;</td>\n";
		}	
	}
	echo "  </tr>\n";

}
?>
</table>