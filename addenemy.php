<?php
  include "checklogin.php";
  include "menu.php";
?>
<script>
function addOption(id,text,value) {
  var option = document.createElement("option");
  option.text = text
  option.value = value;
  id.add(option);
}

function change(from, row, col) {
  var next = document.getElementById('plans[' + row + '][' + col + ']');
  while (next.options.length > 0) {
    next.remove(0);
  }
  switch(from.value) {
  	case "yourhpbelow100":
  	case "yourhpbelow75":
  	case "yourhpbelow50":
  	case "yourhpbelow25":
  	  addOption(next, "Drink a health potion", "drinkhppot");
  	  addOption(next, "Cast a healing spell", "casthealing");
  	  break;
  	case "allyhpbelow100":
  	case "allyhpbelow75":
  	case "allyhpbelow50":
  	case "allyhpbelow25":
  	  addOption(next, "Cast a healing spell", "casthealing");
  	  break;
  	case "notnexttoenemy":
  	  addOption(next, "Move closer to an enemy", "moveclosertoenemy");
  	  addOption(next, "Use a ranged attack", "rangedattack");
      addOption(next, "Use a magic attack", "magicattack");
  	  break;
  	case "nexttoenemy":
      addOption(next, "Use a melee attack", "meleeattack");
      addOption(next, "Move away from the enemy", "moveaway");
  	  break;
  }
}
</script>
<?php

ini_set("display_errors", 1);

if(isset($_POST['name'])) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $race = mysqli_real_escape_string($conn, $_POST['race']);
  $prof = mysqli_real_escape_string($conn, $_POST['prof']);
  $str = mysqli_real_escape_string($conn, $_POST['str']);
  $vit = mysqli_real_escape_string($conn, $_POST['vit']);
  $dex = mysqli_real_escape_string($conn, $_POST['dex']);
  $nce = mysqli_real_escape_string($conn, $_POST['nce']);
  $pie = mysqli_real_escape_string($conn, $_POST['pie']);
  $xp = mysqli_real_escape_string($conn, $_POST['xp']);
  foreach($_POST['plans'] as $plans) {
    $battleplans[] = implode('|', $plans);
  }
  $battleplan = mysqli_real_escape_string($conn, implode("||",$battleplans));

  $hpmult = 1;
  $mpmult = 1;
  switch($race) {
    case "Elf":
      $hpmult = $hpmult - 0.15;
      $mpmult = $mpmult + 0.3;
      break;
    case "Orc":
      $hpmult = $hpmult + 0.2;
      $mpmult = $mpmult - 0.1;
      break;
    case "Human":
      $hpmult = $hpmult + 0.1;
      $mpmult = $mpmult + 0.1;
      break;
    case "Dwarf":
      $hpmult = $hpmult + 0.30;
      $mpmult = $mpmult + 0.15;
      break;
  }
  switch($prof) {
    case "Mage":
      $hpmult = $hpmult - 0.15;
      $mpmult = $mpmult + 0.3;
      break;
    case "Barbarian":
      $hpmult = $hpmult + 0.3;
      $mpmult = $mpmult - 0.15;
      break;
    case "Archer":
      $hpmult = $hpmult + 0.1;
      $mpmult = $mpmult + 0.1;
      break;
    case "Knight":
      $hpmult = $hpmult + 0.20;
      $mpmult = $mpmult - 0.05;
      break;
    case "Priest":
      $hpmult = $hpmult - 0.05;
      $mpmult = $mpmult + 0.2;
      break;
  }
  $maxhp = floor(($con*5 + $str*3) * $hpmult);
  $maxmp = floor(($int*5 + $wis*3) * $mpmult);
  $initiative = $agi*2 + $per;

	echo $name, $race, $prof, $maxhp, $maxhp, $maxmp, $maxmp, $initiative, $str, $vit, $dex, $nce, $pie, $xp, $battleplan;

	mysqli_query($conn, "INSERT INTO Enemies (name, race, prof, maxhp, hp, maxmp, mp, initiative, str, vit, dex, nce, pie, battleplan, xp) VALUES ('$name', '$race', '$prof', '$maxhp', '$maxhp', '$maxmp', '$maxmp', '$initiative', '$str', '$vit', '$dex', '$nce', '$pie', '$xp', '$battleplan')") or die(mysqli_error($conn));

	mysqli_close($conn);

	echo "Enemy Added!";
}
?>
<h1>Add Enemy</h1>
<form name='addenemyfrm' id='addenemyfrm' method='POST' action='addenemy.php'>
<table>
	<tr><td>Name</td><td><input type='text' name='name' required></td></tr>
	<tr><td>Race</td><td><input type='text' name='race' required></td></tr>
	<tr><td>Profession</td><td><input type='text' name='prof' required></td></tr>
	<tr><td>Strength</td><td><input type='text' name='str' required></td></tr>
	<tr><td>Vitality</td><td><input type='text' name='vit' required></td></tr>
	<tr><td>Dexterity</td><td><input type='text' name='dex' required></td></tr>
	<tr><td>Intelligence</td><td><input type='text' name='nce' required></td></tr>
	<tr><td>Piety</td><td><input type='text' name='pie' required></td></tr>
	<tr><td>XP</td><td><input type='text' name='xp' required></td></tr>
	<tr><td>Battleplan</td><td><select name='plans[0][0]' id='plans[0][0]' onchange='change(this,0,1);'>
  <option value=''>Select an option</option>
  <option value='yourhpbelow100'>When your HP is between 75% and 100%</option>
  <option value='yourhpbelow75'>When your HP is between 50% and 75%</option>
  <option value='yourhpbelow50'>When your HP is between 25% and 50%</option>
  <option value='yourhpbelow25'>When your HP is less than 25%</option>
  <option value='allyhpbelow100'>When an ally's HP is between 75% and 100%</option>
  <option value='allyhpbelow75'>When an ally's HP is between 50% and 75%</option>
  <option value='allyhpbelow50'>When an ally's HP is between 25% and 50%</option>
  <option value='allyhpbelow25'>When an ally's HP is less than 25%</option>
  <option value='notnexttoenemy'>When not next to an enemy</option>
  <option value='nexttoenemy'>When next to an enemy</option>
</select>
<select name='plans[0][1]' id='plans[0][1]'></select>
<br>
<select name='plans[1][0]' id='plans[1][0]' onchange='change(this,1,1);'>
  <option value=''>Select an option</option>
  <option value='yourhpbelow100'>When your HP is between 75% and 100%</option>
  <option value='yourhpbelow75'>When your HP is between 50% and 75%</option>
  <option value='yourhpbelow50'>When your HP is between 25% and 50%</option>
  <option value='yourhpbelow25'>When your HP is less than 25%</option>
  <option value='allyhpbelow100'>When an ally's HP is between 75% and 100%</option>
  <option value='allyhpbelow75'>When an ally's HP is between 50% and 75%</option>
  <option value='allyhpbelow50'>When an ally's HP is between 25% and 50%</option>
  <option value='allyhpbelow25'>When an ally's HP is less than 25%</option>
  <option value='notnexttoenemy'>When not next to an enemy</option>
/  <option value='nexttoenemy'>When next to an enemy</option>
</select>
<select name='plans[1][1]' id='plans[1][1]'></select>
<br>
<select name='plans[2][0]' id='plans[2][0]' onchange='change(this,2,1);'>
  <option value=''>Select an option</option>
  <option value='yourhpbelow100'>When your HP is between 75% and 100%</option>
  <option value='yourhpbelow75'>When your HP is between 50% and 75%</option>
  <option value='yourhpbelow50'>When your HP is between 25% and 50%</option>
  <option value='yourhpbelow25'>When your HP is less than 25%</option>
  <option value='allyhpbelow100'>When an ally's HP is between 75% and 100%</option>
  <option value='allyhpbelow75'>When an ally's HP is between 50% and 75%</option>
  <option value='allyhpbelow50'>When an ally's HP is between 25% and 50%</option>
  <option value='allyhpbelow25'>When an ally's HP is less than 25%</option>
  <option value='notnexttoenemy'>When not next to an enemy</option>
  <option value='nexttoenemy'>When next to an enemy</option>
</select>
<select name='plans[2][1]' id='plans[2][1]'></select></td></tr>
</table>
<br>
<input type='submit' value='Add Enemy'>
</form>