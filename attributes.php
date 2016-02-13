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
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
echo "<h1>$hero[name]</h1>";
if(isset($_POST['attribute'])) {
  $aID = mysqli_real_escape_string($conn, $_POST['attribute']);
  if($hero['xp'] < $hero[$aID] * 100) { echo "Not enough xp"; } else {
  	mysqli_query($conn,"UPDATE Hero SET `$aID` = ( `$aID` + 1 ) WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET xp = xp - ($hero[$aID] * 100) WHERE id = '$$cookie[0]'") or die(mysqli_error($conn));
    $hero['xp'] -= ($hero[$aID] * 100);
  	$hero[$aID]++;

    $hpmult = 1;
    $mpmult = 1;
    switch($hero['race']) {
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
    switch($hero['prof']) {
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
    $maxhp = floor(($hero['vit']*5 + $hero['str']*3) * $hpmult);
    $maxmp = floor(($hero['pie']*4 + $hero['nce']) * $mpmult);
  	$initiative = $hero['dex']*2 + $hero['nce'];

  	mysqli_query($conn,"UPDATE Hero SET `maxhp` = $maxhp WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET `hp` = $maxhp WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET `maxmp` = $maxmp WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  	mysqli_query($conn,"UPDATE Hero SET `mp` = $maxmp WHERE id = '$hero[id]'") or die(mysqli_error($conn));
    mysqli_query($conn,"UPDATE Hero SET `initiative` = $initiative WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  }
}
mysqli_close($conn);

echo "<h3>Available xp: " . $hero['xp'] . "</h3>";

echo "<table><tr><th>Attribute</th><th>Level</th><th>Cost</th></tr>";
echo "<tr><td>Strength</td><td>$hero[str]</td><td><a href='javascript:updateAttribute(\"str\");'>" . $hero['str']*100 . "</a></td></tr>";
echo "<tr><td>Vitality</td><td>$hero[vit]</td><td><a href='javascript:updateAttribute(\"vit\");'>" . $hero['vit']*100 . "</a></td></tr>";
echo "<tr><td>Dexterity</td><td>$hero[dex]</td><td><a href='javascript:updateAttribute(\"dex\");'>" . $hero['dex']*100 . "</a></td></tr>";
echo "<tr><td>Intelligence</td><td>$hero[nce]</td><td><a href='javascript:updateAttribute(\"nce\");'>" . $hero['nce']*100 . "</a></td></tr>";
echo "<tr><td>Piety</td><td>$hero[pie]</td><td><a href='javascript:updateAttribute(\"pie\");'>" . $hero['pie']*100 . "</a></td></tr>";
echo "</table>";

echo "<form name='attributefrm' id='attributefrm' method='POST' action='attributes.php'><input name='attribute' type='hidden' value='' id='attributeID'></form>";

?>