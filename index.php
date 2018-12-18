<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<title>Mathe-Projekt-Aufgabe</title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
	<?php 
		if(isset($_POST['level'])){
			echo '<h3>Mathe-Aufgaben, Level: ' . $_POST['level'] . '</h3>';
		} else {
			echo '<h3>Mathe-Aufgaben</h3>';
		}
		
		if(!isset($_POST['senden'])){
			echo '
				<section>
					<form name="form" action="" method="post">
						<span>
							<label>Level:</label>
							<select name="level">
								<option>leicht</option>
								<option>mittel</option>
								<option>schwer</option>
							</select>
						</span>
						<span>
							<label>Anzahl</label>
							<select name="anzahl">
								<option>4</option>
								<option>6</option>
								<option>8</option>
							</select>
						</span>
						<button name="senden" type="submit" value="start">zu den Aufgaben</button>
					</form>
				</section>
			';
		} else {
			if($_POST['senden'] == "start"){
				getTasks($_POST);
			} else {
				if($_POST['senden'] == "auswerten"){
					evaluateTasks($_POST);
				}
			}
		}
		
		function getTasks($data){
			$level = $data['level'];
			$anzahl =  intval($data['anzahl']);
			$senden = $data['senden'];
			
			$aufgaben = [];
			
			$min = 0;
			$max = 0; 
			
			switch ($level){
				case "leicht":
						$min = 1;
						$max = 10;
					break;
				case "mittel":
						$min = 5;
						$max = 50;
					break;
				case "schwer":
						$min = 13;
						$max = 130;
					break;
			}
			
			for ($i = 1; $i <= $anzahl; $i++){
				//Get Operation
				$operationen = ['+', '-', '*', '/'];
				$operation = $operationen[rand(0,3)];
				
				if ($operation == '+' || $operation == '*'){
					$zahl1 = rand($min, $max);
					$zahl2 = rand($min, $max);
					$ergebnis = 0;
					
					switch ($operation){
						case '+':
								$ergebnis = intval($zahl1) + intval($zahl2);
							break;
						case '*':
								$ergebnis = intval($zahl1) * intval($zahl2);
							break;
					}
					array_push($aufgaben, $zahl1 . ',' . $operation . ',' . $zahl2 . ',' . $ergebnis);
				} else {
					if ($operation == '/' || $operation == '-'){						
						$zahl2 = rand(1, 100);
						$ergebnis = rand($min, $max);
						
						if($operation == '/'){
							$zahl1 = $zahl2 * $ergebnis;
						} else {
							if($operation == '-'){
								$zahl1 = $zahl2 + $ergebnis;
							}
						}
						array_push($aufgaben, $zahl1 . ',' . $operation . ',' . $zahl2 . ',' . $ergebnis);
					}
				}
			}
			
			//display aufgaben
			$aufgabenToEvaluate = "";
			
			echo '
				<form name="form" action="" method="post">
					<input type="hidden" name="level" value="' . $level . '"> 
					<input type="hidden" name="anzahl" value="' . $anzahl . '"> 
			';
			
			for ($i = 0; $i <= count($aufgaben) -1; $i++){
				$aufgabe = explode(",", $aufgaben[$i]);
				$num1 = $aufgabe[0];
				$num2 = $aufgabe[2];
				
				if ($num1 < 0){
					$num1 = "(" . $num1 . ")";
				}
				
				if ($num2 < 0){
					$num2 = "(" . $num2 . ")";
				}
				$aufgabe_operation = $aufgabe[1];
				
				echo '<section class="tr">';
					echo "<div>" . ($i+1) . ". Aufgabe:</div>"; 
					echo "<div>" . $num1 . " " . $aufgabe_operation . " " . $num2 . "</div>";
					echo "<div>=</div>";
					echo '<div><input type="text" name="ergebnis' . $i .'"></div>';
				echo "</section>";
				
				//save aufgabe
				$aufgabenToEvaluate .=  $aufgaben[$i] . ';'; 
			}
			
			echo '	
					<section>
						<input type="hidden" name="aufgaben" value="' . $aufgabenToEvaluate . '"> 
						<button name="senden" type="submit" value="auswerten">zur Auswertung</button>
					</section>
				</form>
			';
		}
		
		function evaluateTasks($data){
			$level = $data['level'];
			$anzahl =  intval($data['anzahl']);
			$senden = $data['senden'];
			$aufgaben = explode(";", $data['aufgaben']);
			
			echo '<form name="form" action="" method="post">';
			
				for ($i = 0; $i <= count($aufgaben) -2; $i++){
					$aufgabe = explode(",", $aufgaben[$i]);
					$num1 = $aufgabe[0];
					$num2 = $aufgabe[2];
					
					if ($num1 < 0){
						$num1 = "(" . $num1 . ")";
					}
					
					if ($num2 < 0){
						$num2 = "(" . $num2 . ")";
					}
					
					$aufgabe_operation = $aufgabe[1];
					$ergebnis = $aufgabe[3];
					
					if($data['ergebnis' . $i] == $aufgabe[3]){
						echo '<section class="tr aw">';
							echo '<div>' . ($i+1) . '. Aufgabe:</div>';
							echo '
								<div>
									<b class="r">richtig!</b><br>'
									. $num1 . " " . $aufgabe_operation . " " . $num2 . " = " . $ergebnis . 
								'</div>
							';
						echo '</section>';
					} else {
						if (is_numeric($data['ergebnis' . $i])){
							echo '<section class="tr aw">';
								echo '<div>' . ($i+1) . '. Aufgabe:</div>';
								echo '
									<div>
										<b class="f">leider falsch!</b><br>'
										. $num1 . " " . $aufgabe_operation . " " . $num2 . " != " . $data['ergebnis' . $i] . ' sondern '. $num1 . " " . $aufgabe_operation . " " . $num2 . " = " . $ergebnis .
									'</div>
								';
							echo '</section>';
						} else {
							echo '<section class="tr aw">';
								echo '<div>' . ($i+1) . '. Aufgabe:</div>';
								echo '<div><b class="u">gültige Zahl eingeben.</b></div>';
							echo '</section>';
						}
					}
				}
				
				//auswahl anzeigen
				echo '<section>';
					switch ($level){
						case "leicht":
							echo '
								Level:
								<select name="level">
									<option selected="selected">leicht</option>
									<option>mittel</option>
									<option>schwer</option>
								</select>
							';
							break;
						case "mittel":
							echo '
								Level:
								<select name="level">
									<option>leicht</option>
									<option selected="selected">mittel</option>
									<option>schwer</option>
								</select>
							';
							break;
						case "schwer":
							echo '
								Level:
								<select name="level">
									<option>leicht</option>
									<option>mittel</option>
									<option selected="selected">schwer</option>
								</select>
							';
							break;
					}
					
					switch ($anzahl){
						case 4:
							echo '
								Anzahl:
								<select name="anzahl">
									<option selected="selected">4</option>
									<option>6</option>
									<option>8</option>
								</select>
							';
							break;
						case 6:
							echo '
								Anzahl:
								<select name="anzahl">
									<option>4</option>
									<option selected="selected">6</option>
									<option>8</option>
								</select>
							';
							break;
						case 8:
							echo '
								Anzahl:
								<select name="anzahl">
									<option>4</option>
									<option>6</option>
									<option selected="selected">8</option>
								</select>
							';
							break;
					}
					
					echo '
						<button name="senden" type="submit" value="start">neue Aufgaben</button>
					';
				echo '</section>';		
			echo'</form>';
		}
?>
</body>
</html>
