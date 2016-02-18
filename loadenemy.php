<?php

include "checklogin.php";

echo "<form name='enemysearch' action='loadenemy.php' method='GET'><input type='text' name='id'><input type='submit' value='Submit'></form>";

if(!isset($_GET['id'])) { exit(); }

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$id = mysqli_real_escape_string($conn, $_GET['id']);
$enemy = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Enemies WHERE id = '$id'"));

echo "Enemy:<br>";

echo "<table><tr><th>Name</th><th>Race</th><th>Profession</th><th>Experience</th><th>Party</th><th>Strength</th><th>Vitality</th><th>Dexterity</th><th>Intelligence</th><th>Piety</th><th>Gold</th></tr>";
echo "<tr><td><a href='loadenemy.php?id=$enemy[id]'>$enemy[name]</a></td><td>$enemy[race]</td><td>$enemy[prof]</td><td>$enemy[xp]</td><td><a href='loadparty.php?partyid=$enemy[party]'>$enemy[party]</a></td><td>$enemy[str]</td><td>$enemy[vit]</td><td>$enemy[dex]</td><td>$enemy[nce]</td><td>$enemy[pie]</td><td>$enemy[gold]</td></tr>";
echo "</table>";

echo "<br>";
echo "HP: " . $enemy['maxhp'];
echo "<br>";
echo "MP: " . $enemy['maxmp'];
echo "<br>";
echo "HP Regen: " . $enemy['hpreg'];
echo "<br>";
echo "MP Regen: " . $enemy['mpreg'];
echo "<br><br>";

echo "Initiative: " . $enemy['initiative'];
echo "<br><br>";

//echo ": " . array_sum(array_map(function($temp){return $temp[];},$gear)) . "<br>";
echo "Slashing damage: " . $enemy['sdam'] . "<br>";
echo "Piercing damage: " . $enemy['pdam'] . "<br>";
echo "Bludgeoning damage: " . $enemy['bdam'] . "<br>";
echo "<br>";
echo "Slashing armor: " . $enemy['sarm'] . "<br>";
echo "Piercing armor: " . $enemy['parm'] . "<br>";
echo "Bludgeoning armor: " . $enemy['barm'] . "<br>";

/*echo "<br>Items:<br>";
echo "Main Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'hand' AND owner = '$id'"))['name']) . "<br>";
echo "Off Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 2 AND slot = 'hand' AND owner = '$id'"))['name']) . "<br>";
echo "Head: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'head' AND owner = '$id'"))['name']) . "<br>";
echo "Torso: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'torso' AND owner = '$id'"))['name']) . "<br>";
echo "Arms: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'arms' AND owner = '$id'"))['name']) . "<br>";
echo "Legs: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'legs' AND owner = '$id'"))['name']) . "<br>";
echo "Feet: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'feet' AND owner = '$id'"))['name']);
*/
mysqli_close($conn);

?>