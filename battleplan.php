<script>
// var num = 0;
// function add() {
  // document.getElementById('plans').innerHTML += "<select name='plans'><option value=''>Select an option</option><option value='hpbelow'>When HP is below</option><option value='noenemy'>When not next to an enemy</option></select><br>";
  
  // battleplans[num] = document.createElement("select");
  // battleplans[num].name = "battleplans[" + num + "]";
  // battleplans[num].innerHTML = "<option value='asdf'>asdf</option>"
  // document.body.appendChild(battleplans[num]);
  // num++;
// }
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

include "checklogin.php";

if(isset($_GET['plans'])) {
  foreach($_GET['plans'] as $plans) {
    $battleplans[] = implode('|',$plans);
  }
  $battleplan = implode("||",$battleplans);
  $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  mysqli_query($conn,"UPDATE Hero SET battleplan = '$battleplan' WHERE id = '$cookie[0]'");
  mysqli_close($conn);
}

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$currentplans = mysqli_fetch_assoc(mysqli_query($conn,"SELECT battleplan FROM Hero WHERE id = '$cookie[0]'"));
mysqli_close($conn);

$currentplan = explode("||",$currentplans['battleplan']);
foreach($currentplan as $currents) {
  $current[] = explode('|',$currents);
}

print_r($current);

echo "<form name='battleplan' action='battleplan.php' method='GET'>";
echo "<select name='plans[0][0]' id='plans[0][0]' onchange='change(this,0,1);'>
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
</select>";
echo "<select name='plans[0][1]' id='plans[0][1]'></select>";
echo "<br>";
echo "<select name='plans[1][0]' id='plans[1][0]' onchange='change(this,1,1);'>
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
</select>";
echo "<select name='plans[1][1]' id='plans[1][1]'></select>";
echo "<br>";
echo "<select name='plans[2][0]' id='plans[2][0]' onchange='change(this,2,1);'>
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
</select>";
echo "<select name='plans[2][1]' id='plans[2][1]'></select>";
echo "<br>";
echo "<button type='button' onclick='add()'>Add</button><input type='submit' value='Save'></form>";

?>