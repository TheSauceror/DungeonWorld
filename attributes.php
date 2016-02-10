<script>
function updateAttribute(aID) {
  document.getElementById('attributeID').value = aID;
  document.getElementById('attributefrm').submit();
}
</script>

<?php

include "checklogin.php";

ini_set("display_errors", 1);

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE heroid = '$cookie[0]'"));
echo "<h1>$hero[heroname]</h1>";
if(isset($_POST['attribute'])) {
  $aID = mysqli_real_escape_string($conn, $_POST['attribute']);
  if($hero['xp'] < $hero[$aID] * 100) { echo "Not enough xp"; } else {
  	mysqli_query($conn,"UPDATE Hero SET `$aID` = ( `$aID` + 1 ) WHERE name = '$heroname'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET xp = xp - ($hero[$aID] * 100) WHERE name = '$heroname'") or die(mysqli_error($conn));
    $hero['xp'] -= ($hero[$aID] * 100);
  	$hero[$aID]++;

    $hpmult = 1;
    $mpmult = 1;
    switch($hero['herorace']) {
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
    switch($hero['heroprof']) {
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
    $maxhp = floor(($hero['con']*5 + $hero['str']*3) * $hpmult);
    $maxmp = floor(($hero['int']*5 + $hero['wis']*3) * $mpmult);
  	$initiative = $hero['agi']*2 + $hero['per'];

  	mysqli_query($conn,"UPDATE Hero SET `maxhp` = $maxhp WHERE heroid = '$hero[id]'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET `hp` = $maxhp WHERE heroid = '$hero[id]'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET `maxmp` = $maxmp WHERE heroid = '$hero[id]'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET `mp` = $maxmp WHERE heroid = '$hero[id]'") or die(mysqli_error($conn));
    mysqli_query($conn,"UPDATE Hero SET `initiative` = $initiative WHERE heroid = '$hero[id]'") or die(mysqli_error($conn));
  }
}
mysqli_close($conn);

echo "<h3>Available xp: " . $hero['xp'] . "</h3>";

echo "<table><tr><th>Attribute</th><th>Level</th><th>Cost</th></tr>";
echo "<tr><td>Strength</td><td>$hero[str]</td><td><a href='javascript:updateAttribute(\"str\");'>" . $hero['str']*100 . "</a></td></tr>";
echo "<tr><td>Constitution</td><td>$hero[con]</td><td><a href='javascript:updateAttribute(\"con\");'>" . $hero['con']*100 . "</a></td></tr>";
echo "<tr><td>Intelligence</td><td>$hero[int]</td><td><a href='javascript:updateAttribute(\"int\");'>" . $hero['int']*100 . "</a></td></tr>";
echo "<tr><td>Wisdom</td><td>$hero[wis]</td><td><a href='javascript:updateAttribute(\"wis\");'>" . $hero['wis']*100 . "</a></td></tr>";
echo "<tr><td>Dexterity</td><td>$hero[dex]</td><td><a href='javascript:updateAttribute(\"dex\");'>" . $hero['dex']*100 . "</a></td></tr>";
echo "<tr><td>Agility</td><td>$hero[agi]</td><td><a href='javascript:updateAttribute(\"agi\");'>" . $hero['agi']*100 . "</a></td></tr>";
echo "<tr><td>Perception</td><td>$hero[per]</td><td><a href='javascript:updateAttribute(\"per\");'>" . $hero['per']*100 . "</a></td></tr>";
echo "<tr><td>Charisma</td><td>$hero[cha]</td><td><a href='javascript:updateAttribute(\"cha\");'>" . $hero['cha']*100 . "</a></td></tr>";
echo "<tr><td>Actions</td><td>$hero[act]</td><td><a href='javascript:updateAttribute(\"act\");'>" . $hero['act']*100 . "</a></td></tr>";
echo "</table>";

echo "<form name='attributefrm' id='attributefrm' method='POST' action='attributes.php'><input name='attribute' type='hidden' value='' id='attributeID'></form>";

?>