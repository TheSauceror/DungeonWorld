<?php

include "checklogin.php";

echo "<form name='herosearch' action='loadhero.php' method='GET'><input type='text' name='heroid'><input type='submit' value='Submit'></form>";

if(!isset($_GET['heroid'])) { exit(); }

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$heroid = mysqli_real_escape_string($conn, $_GET['heroid']);
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE Hero.heroid = '$heroid' AND Hero.heroparty = Party.partyid"));

echo "Hero:<br>";

echo "<table><tr><th>Name</th><th>Race</th><th>Profession</th><th>Experience</th><th>Party</th><th>Strength</th><th>Intelligence</th><th>Dexterity</th><th>Agility</th><th>Wisdom</th><th>Perception</th><th>Action</th><th>Constitution</th><th>Charisma</th><th>Gold</th></tr>";
echo "<tr><td><a href='loadhero.php?heroid=$hero[heroid]'>$hero[heroname]</a></td><td>$hero[herorace]</td><td>$hero[heroprof]</td><td>$hero[xp]</td><td><a href='loadparty.php?partyid=$hero[partyid]'>$hero[partyname]</a></td><td>$hero[str]</td><td>$hero[int]</td><td>$hero[dex]</td><td>$hero[agi]</td><td>$hero[wis]</td><td>$hero[per]</td><td>$hero[act]</td><td>$hero[con]</td><td>$hero[cha]</td><td>$hero[gold]</td></tr>";
echo "</table>";

function getEquips($col,$hero) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  return mysqli_fetch_assoc(mysqli_query($conn,"SELECT $col FROM Item WHERE equip > 0 AND owner = '$hero'"))[$col];
  mysqli_close($conn);
}

echo "<br>";
echo "<a href='sendmessage.php?to=$heroid' target='_blank'>Send a message</a><br>";
echo "<br>";
echo "HP: " . $hero['maxhp'];
echo "<br>";
echo "MP: " . $hero['maxmp'];
echo "<br>";
echo "HP Regen: " . getEquips("SUM(hpreg)", $heroid);
echo "<br>";
echo "MP Regen: " . getEquips("SUM(mpreg)", $heroid);
echo "<br><br>";

echo "Initiative: " . $hero['initiative'];
echo "<br><br>";


//echo ": " . array_sum(array_map(function($temp){return $temp[];},$gear)) . "<br>";
echo "Slashing damage: " . getEquips("SUM(sdam)",$heroid) . "<br>";
echo "Piercing damage: " . getEquips("SUM(pdam)",$heroid) . "<br>";
echo "Bludgeoning damage: " . getEquips("SUM(bdam)",$heroid) . "<br>";
echo "<br>";
echo "Slashing armor: " . getEquips("SUM(sarm)",$heroid) . "<br>";
echo "Piercing armor: " . getEquips("SUM(parm)",$heroid) . "<br>";
echo "Bludgeoning armor: " . getEquips("SUM(barm)",$heroid) . "<br>";

echo "<br>Items:<br>";
echo "Main Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'hand' AND owner = '$heroid'"))['name']) . "<br>";
echo "Off Hand: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 2 AND slot = 'hand' AND owner = '$heroid'"))['name']) . "<br>";
echo "Head: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'head' AND owner = '$heroid'"))['name']) . "<br>";
echo "Torso: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'torso' AND owner = '$heroid'"))['name']) . "<br>";
echo "Arms: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'arms' AND owner = '$heroid'"))['name']) . "<br>";
echo "Legs: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'legs' AND owner = '$heroid'"))['name']) . "<br>";
echo "Feet: " . trim(mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(pre, ' ', base, ' ', suf) as name FROM Item WHERE equip = 1 AND slot = 'feet' AND owner = '$heroid'"))['name']);

mysqli_close($conn);

?>