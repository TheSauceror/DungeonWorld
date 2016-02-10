<?php

include "checklogin.php";

echo "<form name='enemysearch' action='loadenemy.php' method='GET'><input type='text' name='enemyid'><input type='submit' value='Submit'></form>";

if(!isset($_GET['enemyid'])) { exit(); }

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$enemyid = mysqli_real_escape_string($conn, $_GET['enemyid']);
$enemy = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Enemies WHERE enemyid = '$enemyid'"));

echo "Enemy:<br>";

echo "<table><tr><th>Name</th><th>Race</th><th>Profession</th><th>Experience</th><th>Party</th><th>Strength</th><th>Intelligence</th><th>Dexterity</th><th>Agility</th><th>Wisdom</th><th>Perception</th><th>Action</th><th>Constitution</th><th>Charisma</th><th>Gold</th></tr>";
echo "<tr><td><a href='loadenemy.php?enemyid=$enemy[enemyid]'>$enemy[enemyname]</a></td><td>$enemy[enemyrace]</td><td>$enemy[enemyprof]</td><td>$enemy[xp]</td><td><a href='loadparty.php?partyid=$enemy[party]'>$enemy[party]</a></td><td>$enemy[str]</td><td>$enemy[int]</td><td>$enemy[dex]</td><td>$enemy[agi]</td><td>$enemy[wis]</td><td>$enemy[per]</td><td>$enemy[act]</td><td>$enemy[con]</td><td>$enemy[cha]</td><td>$enemy[gold]</td></tr>";
echo "</table>";

function getEquips($col,$enemy) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  return mysqli_fetch_assoc(mysqli_query($conn,"SELECT $col FROM Item WHERE equip > 0 AND owner = '$enemy'"))[$col];
  mysqli_close($conn);
}

echo "<br>";
echo "<a href='sendmessage.php?to=$enemyid' target='_blank'>Send a message</a><br>";
echo "<br>";
echo "HP: " . $enemy['maxhp'];
echo "<br>";
echo "MP: " . $enemy['maxmp'];
echo "<br>";
echo "HP Regen: " . getEquips("SUM(hpreg)", $enemyid);
echo "<br>";
echo "MP Regen: " . getEquips("SUM(mpreg)", $enemyid);
echo "<br><br>";

echo "Initiative: " . $enemy['initiative'];
echo "<br><br>";

//echo ": " . array_sum(array_map(function($temp){return $temp[];},$gear)) . "<br>";
echo "Slashing damage: " . getEquips("SUM(sdam)",$enemyid) . "<br>";
echo "Piercing damage: " . getEquips("SUM(pdam)",$enemyid) . "<br>";
echo "Bludgeoning damage: " . getEquips("SUM(bdam)",$enemyid) . "<br>";
echo "<br>";
echo "Slashing armor: " . getEquips("SUM(sarm)",$enemyid) . "<br>";
echo "Piercing armor: " . getEquips("SUM(parm)",$enemyid) . "<br>";
echo "Bludgeoning armor: " . getEquips("SUM(barm)",$enemyid) . "<br>";

echo "<br>Items:<br>";
echo "Main Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'hand' AND owner = '$enemyid'"))['name']) . "<br>";
echo "Off Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 2 AND slot = 'hand' AND owner = '$enemyid'"))['name']) . "<br>";
echo "Head: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'head' AND owner = '$enemyid'"))['name']) . "<br>";
echo "Torso: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'torso' AND owner = '$enemyid'"))['name']) . "<br>";
echo "Arms: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'arms' AND owner = '$enemyid'"))['name']) . "<br>";
echo "Legs: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'legs' AND owner = '$enemyid'"))['name']) . "<br>";
echo "Feet: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'feet' AND owner = '$enemyid'"))['name']);

mysqli_close($conn);

?>