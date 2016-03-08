<script>
function updateAttribute(aID) {
  document.getElementById('attributeID').value = aID;
  document.getElementById('attributefrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

//echo "<form name='herosearch' action='profile.php' method='GET'><input type='text' name='id'><input type='submit' value='Submit'></form>";

$id = $cookie[0];

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(isset($_POST['mainhand'])) {
  //protect these inputs  from injection
  //protect other people items from f12ing
  mysqli_query($conn, "UPDATE Inventory SET equip = 0 WHERE owner = '$id'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[mainhand]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 2 WHERE inventoryid = '$_POST[offhand]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[head]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[torso]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[arms]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[legs]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[feet]'");
}

if(isset($_GET['id'])) {
  $id = mysqli_real_escape_string($conn, $_GET['id']);
}

$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE Hero.id = '$id' AND Hero.party = Party.partyid"));

echo "<h1>$hero[name] the $hero[race] $hero[prof]</h1>";

if(isset($_POST['attribute'])) {
  $aID = mysqli_real_escape_string($conn, $_POST['attribute']);
  if($hero['gold'] < $hero[$aID] * 100) { echo "Not enough gold"; } else {
    mysqli_query($conn,"UPDATE Hero SET `$aID` = ( `$aID` + 1 ) WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
    mysqli_query($conn,"UPDATE Hero SET `gold` = gold - ($hero[$aID] * 100) WHERE id = '$cookie[0]'") or die(mysqli_error($conn));

    calculateHPMPInit($cookie[0]);

    $hero['gold'] -= $hero[$aID] * 100;
    $hero[$aID]++;
  }
}

if($id == $cookie[0]) {
  echo "<h3>Available gold: " . $hero['gold'] . "</h3>";
} else {
  echo "<a href='sendmessage.php?to=$id' target='_blank'><h3>Send a message</h3></a>";
}

echo "<table><tr><th>Attribute</th><th>Level</th>";
if($id == $cookie[0]) { echo "<th>Training Cost</th></tr>"; }
echo "<tr><td>Strength</td><td>$hero[str]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"str\");'>" . $hero['str']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Vitality</td><td>$hero[vit]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"vit\");'>" . $hero['vit']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Dexterity</td><td>$hero[dex]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"dex\");'>" . $hero['dex']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Intelligence</td><td>$hero[nce]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"nce\");'>" . $hero['nce']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Piety</td><td>$hero[pie]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"pie\");'>" . $hero['pie']*100 . " gold</a>"; }
echo "</td></tr>";
echo "</table>";

echo "<form name='attributefrm' id='attributefrm' method='POST' action='profile.php'><input name='attribute' type='hidden' value='' id='attributeID'></form>";

echo "<br>";
echo "HP: " . $hero['maxhp'];
echo "<br>";
echo "MP: " . $hero['maxmp'];
echo "<br>";
echo "Initiative: " . $hero['initiative'];
echo "<br><br>";
echo "HP regen: " . getAllItemStats($id, "hpreg");
echo "<br>";
echo "MP regen: " . getAllItemStats($id, "mpreg");
echo "<br><br>";

//echo ": " . array_sum(array_map(function($temp){return $temp[];},$gear)) . "<br>";
echo "Slashing damage: " . getAllItemStats($id, "sdam") . "<br>";
echo "Piercing damage: " . getAllItemStats($id, "pdam") . "<br>";
echo "Bludgeoning damage: " . getAllItemStats($id, "bdam") . "<br>";
echo "<br>";
echo "Slashing armor: " . getAllItemStats($id, "sarm") . "<br>";
echo "Piercing armor: " . getAllItemStats($id, "parm") . "<br>";
echo "Bludgeoning armor: " . getAllItemStats($id, "barm") . "<br>";

echo "<br>Items:<br>";

if($id == $cookie[0]) {
  echo "<form name='equipfrm' id='equipfrm' method='POST' action='profile.php'>";
  echo "<table>";
  echo "<tr><td>Main Hand:</td><td><select name='mainhand' onChange='this.form.submit();'>"; getAllSlot($id, 'Hand', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Hand', 1) . "</td></tr>";
  echo "<tr><td>Off Hand:</td><td><select name='offhand' onChange='this.form.submit();'>"; getAllSlot($id, 'Hand', 2); echo "</select></td><td>" . getItemDes(0, $id, 'Hand', 2) . "</td></tr>";
  echo "<tr><td>Head:</td><td><select name='head' onChange='this.form.submit();'>"; getAllSlot($id, 'Head', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Head', 1) . "</td></tr>";
  echo "<tr><td>Torso:</td><td><select name='torso' onChange='this.form.submit();'>"; getAllSlot($id, 'Torso', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Torso', 1) . "</td></tr>";
  echo "<tr><td>Arms:</td><td><select name='arms' onChange='this.form.submit();'>"; getAllSlot($id, 'Arms', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Arms', 1) . "</td></tr>";
  echo "<tr><td>Legs:</td><td><select name='legs' onChange='this.form.submit();'>"; getAllSlot($id, 'Legs', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Legs', 1) . "</td></tr>";
  echo "<tr><td>Feet:</td><td><select name='feet' onChange='this.form.submit();'>"; getAllSlot($id, 'Feet', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Feet', 1) . "</td></tr>";
  echo "</table>";
  echo "</form>";
} else {
  echo "Main Hand: " . getItemName('Hand', $id, 1) . " - " . getItemDes(0, $id, 'Hand', 1) . "<br>";
  echo "Off Hand: " . getItemName('Hand', $id, 2) . " - " . getItemDes(0, $id, 'Hand', 2) . "<br>";
  echo "Head: " . getItemName('Head', $id, 1) . " - " . getItemDes(0, $id, 'Head', 1) . "<br>";
  echo "Torso: " . getItemName('Torso', $id, 1) . " - " . getItemDes(0, $id, 'Torso', 1) . "<br>";
  echo "Arms: " . getItemName('Arms', $id, 1) . " - " . getItemDes(0, $id, 'Arms', 1) . "<br>";
  echo "Legs: " . getItemName('Legs', $id, 1) . " - " . getItemDes(0, $id, 'Legs', 1) . "<br>";
  echo "Feet: " . getItemName('Feet', $id, 1) . " - " . getItemDes(0, $id, 'Feet', 1) . "<br>";
}

mysqli_close($conn);

function getAllSlot($hero, $slot, $equip) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd"); 
  $slotitems = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND ItemBase.slot = '$slot' AND Inventory.market = '0'");
  echo "<option value=''>----------</option>";
  while($row = mysqli_fetch_assoc($slotitems)) {
    echo "<option value='" . $row['inventoryid'] . "'";
    if($row['equip'] == $equip) { echo " selected"; }
    echo ">";
    if($row['prefixlevel'] > 0) { echo $row['prefixname'] . "(" . $row['prefixlevel'] . ") "; }
    echo $row['basename'] . "(" . $row['baselevel'] . ")";
    if($row['suffixlevel'] > 0) { echo " " . $row['suffixname'] . "(" . $row['suffixlevel'] . ")"; }
    echo "</option>";
  }
  mysqli_close($conn);
  return;
}

function getItemName($slot, $hero, $equip) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  //SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND Inventory.slot='$slot' AND Inventory.equip = '$equip'
//  $itemname = mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(prefixname, ' ', basename, ' ', suffixname) AS 'fullname' FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND ItemBase.slot='$slot' AND Inventory.equip = '$equip'"))['fullname'];
//  print_r($itemname);
  $itemname = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND ItemBase.slot='$slot' AND Inventory.equip = '$equip'"));
  //return mysqli_fetch_assoc(mysqli_query($conn,"SELECT $col FROM Item WHERE equip > 0 AND owner = '$hero'"))[$col];
  mysqli_close($conn);
//  return $itemname;
  $fullitemname = "";
  if($itemname['prefixlevel'] > 0) { $fullitemname .= $itemname['prefixname'] . "(" . $itemname['prefixlevel'] . ") "; }
  if($itemname['baselevel'] > 0) { $fullitemname .= $itemname['basename'] . "(" . $itemname['baselevel'] . ")"; }
  if($itemname['suffixlevel'] > 0) { $fullitemname .= " " . $itemname['suffixname'] . "(" . $itemname['suffixlevel'] . ")"; }
  return trim($fullitemname);
}

?>