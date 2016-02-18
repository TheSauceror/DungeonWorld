<?php

include "checklogin.php";

echo "<form name='herosearch' action='loadhero.php' method='GET'><input type='text' name='id'><input type='submit' value='Submit'></form>";

if(!isset($_GET['id'])) { exit(); }

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$id = mysqli_real_escape_string($conn, $_GET['id']);
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE Hero.id = '$id' AND Hero.party = Party.partyid"));

echo "Hero:<br>";

echo "<table><tr><th>Name</th><th>Race</th><th>Profession</th><th>Experience</th><th>Party</th><th>Strength</th><th>Vitality</th><th>Dexterity</th><th>Intelligence</th><th>Piety</th><th>Gold</th></tr>";
echo "<tr><td><a href='loadhero.php?id=$hero[id]'>$hero[name]</a></td><td>$hero[race]</td><td>$hero[prof]</td><td>$hero[xp]</td><td><a href='loadparty.php?partyid=$hero[partyid]'>$hero[partyname]</a></td><td>$hero[str]</td><td>$hero[vit]</td><td>$hero[dex]</td><td>$hero[nce]</td><td>$hero[pie]</td><td>$hero[gold]</td></tr>";
echo "</table>";

function getItemName($slot, $hero, $equip) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	//SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND Inventory.slot='$slot' AND Inventory.equip = '$equip'
//  $itemname = mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(prefixname, ' ', basename, ' ', suffixname) AS 'fullname' FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND ItemBase.slot='$slot' AND Inventory.equip = '$equip'"))['fullname'];
//  print_r($itemname);
  $itemname = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND ItemBase.slot='$slot' AND Inventory.equip = '$equip'"));
  //return mysqli_fetch_assoc(mysqli_query($conn,"SELECT $col FROM Item WHERE equip > 0 AND owner = '$hero'"))[$col];
  mysqli_close($conn);
//  return $itemname;
  return trim($itemname['prefixname'] . " " . $itemname['basename'] . " " . $itemname['suffixname']);
}

function getAllItemStats($hero, $stat) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	//$equippeditems = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(ItemBase.$stat) + SUM(ItemPrefix.$stat) + SUM(ItemSuffix.$stat) AS 'allequippedstats' FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND Inventory.equip > 0"));
	$equippeditems = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND Inventory.equip > 0");
  $equippedstats = 0;
  while($row = mysqli_fetch_assoc($equippeditems)) {
    $equippedstats += max(0, $row["prefix".$stat]) + max(0, $row["base".$stat]) + max(0, $row["suffix".$stat]);
  }
  //while($row = mysqli_fetch_assoc($tempequippeditems)) {
		//$equippeditems[] = $row;
	//}
  //return mysqli_fetch_assoc(mysqli_query($conn,"SELECT $col FROM Item WHERE equip > 0 AND owner = '$hero'"))[$col];
  mysqli_close($conn);
  return $equippedstats;
}

echo "<br>";
echo "<a href='sendmessage.php?to=$id' target='_blank'>Send a message</a><br>";
echo "<br>";
echo "HP: " . $hero['maxhp'];
echo "<br>";
echo "MP: " . $hero['maxmp'];
echo "<br>";
echo "Initiative: " . $hero['initiative'];
echo "<br><br>";
echo "HP Regen: " . getAllItemStats($id, "hpreg");
echo "<br>";
echo "MP Regen: " . getAllItemStats($id, "mpreg");
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
echo "Main Hand: " . getItemName('hand', $id, 1) . "<br>";
echo "Off Hand: " . getItemName('hand', $id, 2) . "<br>";
echo "Head: " . getItemName('head', $id, 1) . "<br>";
echo "Torso: " . getItemName('torso', $id, 1) . "<br>";
echo "Arms: " . getItemName('arms', $id, 1) . "<br>";
echo "Legs: " . getItemName('legs', $id, 1) . "<br>";
echo "Feet: " . getItemName('feet', $id, 1) . "<br>";
//echo "Main Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'hand' AND owner = '$id'"))['name']) . "<br>";
//echo "Off Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 2 AND slot = 'hand' AND owner = '$id'"))['name']) . "<br>";
//echo "Head: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'head' AND owner = '$id'"))['name']) . "<br>";
//echo "Torso: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'torso' AND owner = '$id'"))['name']) . "<br>";
//echo "Arms: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'arms' AND owner = '$id'"))['name']) . "<br>";
//echo "Legs: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'legs' AND owner = '$id'"))['name']) . "<br>";
//echo "Feet: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'feet' AND owner = '$id'"))['name']);

mysqli_close($conn);

?>