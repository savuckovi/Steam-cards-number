<?php

ini_set('max_execution_time', 120);

$skyrim = file_get_contents("http://steamcommunity.com/market/listings/753/245070-Skyrim");
$chivalry = file_get_contents("http://steamcommunity.com/market/listings/753/245070-Chivalry%3A%20Medieval%20Warfare");
$prison = file_get_contents("http://steamcommunity.com/market/listings/753/245070-Prison%20Architect");

$dom = new DOMDocument();
if (@$dom->loadHTML($skyrim)) {
	$skyrim_uk = str_replace(",","",$dom->getElementById('searchResults_total')->nodeValue);
}
if (@$dom->loadHTML($chivalry)) {
	$chivalry_uk = str_replace(",","",$dom->getElementById('searchResults_total')->nodeValue);
}
if (@$dom->loadHTML($prison)) {
	$prison_uk = str_replace(",","",$dom->getElementById('searchResults_total')->nodeValue);
}

$con=mysqli_connect("localhost","root","","steam");

if (mysqli_connect_errno($con))
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
else
	{	
		$date = date("Y-m-d H:i:s");
		 
		$result = mysqli_query($con,"SELECT * FROM cards");
		echo "<div align='center'><table border='1' style='text-align: center'><form action='steam.php' method='get'><tr><td>Igra</td><td>Kolicina</td><td>Promjena</td><td style='width: 200px'>Vrijeme (spremljeno)</td><td style='width: 200px'>Vrijeme (sada)</td><td>Vrijeme (razlika)</td><td><button type='Submit' name='brisi' value='true'>Obrisi</button></td></tr>";

		while($row = mysqli_fetch_array($result))
			{
			
			
				if ($row['igra'] == "Skyrim") $novi = $skyrim_uk;
				else if ($row['igra'] == "Chivalry") $novi = $chivalry_uk;
				else if ($row['igra'] == "Prison architect") $novi = $prison_uk;
				
				$razlika = $novi - $row['broj_karata'];

				if ($razlika>0) $razlika = "+" . $razlika;
				
				$vrijeme_baza = strtotime($row['vrijeme']);
				$vrijeme_sada = strtotime(date("Y-m-d H:i:s"));
				$vrijeme_razlika = $vrijeme_sada-$vrijeme_baza;
				
				if ($vrijeme_razlika >= 60){
					$minute = (int)($vrijeme_razlika/60);
					
					if ($minute>=60){
						
						$hours = floor($vrijeme_razlika / 3600);
						$minutes = floor(($vrijeme_razlika / 60) % 60);
						$seconds = $vrijeme_razlika % 60;
						
						$vrijeme_raz = $hours . "h " . $minutes . "min " . $seconds . "s";
					} else{
						$vrijeme_raz = $minute . "min " . ($vrijeme_razlika % 60) . "s";
					}
				}else{
					$vrijeme_raz = $vrijeme_razlika . "s";
				}
				
				echo "<tr><td>". $row['igra'] . "</td><td>" . $row['broj_karata'] . "</td><td>" . $razlika . "</td><td>" . $row['vrijeme'] . "</td><td>". date("Y-m-d H:i:s") . "</td><td>" . $vrijeme_raz . "</td><td style='text-align: center'><input type='checkbox' name='za_brisanje[]' value='". $row['id'] ."'><br></td></tr>";			

			}

		echo "</form></table></div>";

		if (!$ima) mysqli_query($con, "INSERT INTO cards (game, card_num, time) VALUES ('Skyrim','$skyrim_uk','$date'), ('Chivalry','$chivalry_uk','$date'),('Prison architect','$prison_uk','$date')");

		echo "<div align='center'><table border='1' style='text-align: center'><form action='steam.php' method='get'><tr><td>Store</td><td>Name</td></td></tr>";
		$storesDb = mysqli_query($con,"SELECT * FROM stores");
		
		while($row = mysqli_fetch_array($storesDb))
		{
			echo "<tr><td>". $row['id'] . "</td><td>" . $row['name'] . "</td></tr>";			
		}
		echo "</form></table></div>";

		mysqli_close($con);
	}

?>
