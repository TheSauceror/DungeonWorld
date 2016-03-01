<?php

include "checklogin.php";

echo "<form name='partysearch' action='loadparty.php' method='GET'><input type='text' name='partyid'><input type='submit' value='Submit'></form>";

if(!isset($_GET['partyid'])) { exit(); }

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$partyid = mysqli_real_escape_string($conn, trim($_GET['partyid']));
$heroes = mysqli_query($conn,"SELECT * FROM Hero, Party WHERE Hero.party = Party.partyid AND Party.partyid = '$partyid'");
//$heroes = mysqli_query($conn,"SELECT * FROM Hero WHERE heroparty = '$partyid'");
while($row = mysqli_fetch_assoc($heroes)) {
  $partyname = $row['partyname'];
  $partycd = $row['cd'];
  $heroesname[] = $row['name'];
  $heroesid[] = $row['id'];
}
mysqli_close($conn);

echo "<h1>$partyname</h1>";

echo "<table><tr><th>Cooldown</th><th>Hero 1</th><th>Hero 2</th><th>Hero 3</th><th>Hero 4</th><th>Hero 5</th><th>Hero 6</th></tr>";
echo "<tr><td>$partycd</td><td><a href='profile.php?id=$heroesid[0]'>$heroesname[0]</a></td><td><a href='profile.php?id=$heroesid[1]'>$heroesname[1]</a></td><td><a href='profile.php?id=$heroesid[2]'>$heroesname[2]</a></td><td><a href='profile.php?id=$heroesid[3]'>$heroesname[3]</a></td><td><a href='profile.php?id=$heroesid[4]'>$heroesname[4]</a></td><td><a href='profile.php?id=$heroesid[5]'>$heroesname[5]</a></td></tr>";
echo "</table>";

?>