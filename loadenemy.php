<?php

include "checklogin.php";
include "menu.php";

echo "<form name='enemysearch' action='loadenemy.php' method='GET'><input type='text' name='id'><input type='submit' value='Submit'></form>";

if(!isset($_GET['id'])) { exit(); }

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$id = mysqli_real_escape_string($conn, $_GET['id']);
$enemy = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Enemies WHERE id = '$id'"));

echo "<div class='parchment'><h3>Enemy:</h3>";
echo "<table><tr><th>Name</th><th>Race</th><th>Profession</th><th>Gold</th><th>Party</th><th>Strength</th><th>Vitality</th><th>Dexterity</th><th>Intelligence</th><th>Piety</th></tr>";
echo "<tr><td><a href='loadenemy.php?id=$enemy[id]'>$enemy[name]</a></td><td>$enemy[race]</td><td>$enemy[prof]</td><td>$enemy[gold]</td><td><a href='loadparty.php?partyid=$enemy[party]'>$enemy[party]</a></td><td>$enemy[str]</td><td>$enemy[vit]</td><td>$enemy[dex]</td><td>$enemy[nce]</td><td>$enemy[pie]</td></tr>";
echo "</table>";

echo "<br>";
echo "HP: " . $enemy['maxhp'];
echo "<br>";
echo "MP: " . $enemy['maxmp'];
echo "<br>";
echo "Initiative: " . $enemy['initiative'];
echo "<br><br>";
echo "HP Regen: " . $enemy['hpreg'];
echo "<br>";
echo "MP Regen: " . $enemy['mpreg'];
echo "<br><br>";

//echo ": " . array_sum(array_map(function($temp){return $temp[];},$gear)) . "<br>";
echo "Slashing damage: " . $enemy['sdam'] . "<br>";
echo "Piercing damage: " . $enemy['pdam'] . "<br>";
echo "Bludgeoning damage: " . $enemy['bdam'] . "<br>";
echo "<br>";
echo "Slashing armor: " . $enemy['sarm'] . "<br>";
echo "Piercing armor: " . $enemy['parm'] . "<br>";
echo "Bludgeoning armor: " . $enemy['barm'] . "<br>";
echo "</div>";

mysqli_close($conn);

?>