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

function getEquips($col,$hero) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  return mysqli_fetch_assoc(mysqli_query($conn,"SELECT $col FROM Item WHERE equip > 0 AND owner = '$hero'"))[$col];
  mysqli_close($conn);
}

echo "<br>";
echo "<a href='sendmessage.php?to=$id' target='_blank'>Send a message</a><br>";
echo "<br>";
echo "HP: " . $hero['maxhp'];
echo "<br>";
echo "MP: " . $hero['maxmp'];
echo "<br>";
echo "HP Regen: " . getEquips("SUM(hpreg)", $id);
echo "<br>";
echo "MP Regen: " . getEquips("SUM(mpreg)", $id);
echo "<br><br>";

echo "Initiative: " . $hero['initiative'];
echo "<br><br>";


//echo ": " . array_sum(array_map(function($temp){return $temp[];},$gear)) . "<br>";
echo "Slashing damage: " . getEquips("SUM(sdam)",$id) . "<br>";
echo "Piercing damage: " . getEquips("SUM(pdam)",$id) . "<br>";
echo "Bludgeoning damage: " . getEquips("SUM(bdam)",$id) . "<br>";
echo "<br>";
echo "Slashing armor: " . getEquips("SUM(sarm)",$id) . "<br>";
echo "Piercing armor: " . getEquips("SUM(parm)",$id) . "<br>";
echo "Bludgeoning armor: " . getEquips("SUM(barm)",$id) . "<br>";

echo "<br>Items:<br>";
echo "Main Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'hand' AND owner = '$id'"))['name']) . "<br>";
echo "Off Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 2 AND slot = 'hand' AND owner = '$id'"))['name']) . "<br>";
echo "Head: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'head' AND owner = '$id'"))['name']) . "<br>";
echo "Torso: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'torso' AND owner = '$id'"))['name']) . "<br>";
echo "Arms: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'arms' AND owner = '$id'"))['name']) . "<br>";
echo "Legs: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'legs' AND owner = '$id'"))['name']) . "<br>";
echo "Feet: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'feet' AND owner = '$id'"))['name']);

mysqli_close($conn);

?>