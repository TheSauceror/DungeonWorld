<?php

include "checklogin.php";

echo "<form name='partysearch' action='loadparty.php' method='GET'><input type='text' name='partyid'><input type='submit' value='Submit'></form>";

if(!isset($_GET['partyid'])) { exit(); }

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$partyid = mysqli_real_escape_string($conn, trim($_GET['partyid']));
$heroes = mysqli_query($conn,"SELECT * FROM Hero, Party WHERE Hero.heroparty = Party.partyid AND Party.partyid = '$partyid'");
//$heroes = mysqli_query($conn,"SELECT * FROM Hero WHERE heroparty = '$partyid'");
while($row = mysqli_fetch_assoc($heroes)) {
  $partyname = $row['partyname'];
  $partycd = $row['cd'];
  $heroesname[] = $row['heroname'];
  $heroesid[] = $row['heroid'];
}
mysqli_close($conn);

echo "<h1>$partyname</h1>";

echo "<table><tr><th>Cooldown</th><th>Hero 1</th><th>Hero 2</th><th>Hero 3</th><th>Hero 4</th><th>Hero 5</th><th>Hero 6</th></tr>";
echo "<tr><td>$partycd</td><td><a href='loadhero.php?heroid=$heroesid[0]'>$heroesname[0]</a></td><td><a href='loadhero.php?heroid=$heroesid[1]'>$heroesname[1]</a></td><td><a href='loadhero.php?heroid=$heroesid[2]'>$heroesname[2]</a></td><td><a href='loadhero.php?heroid=$heroesid[3]'>$heroesname[3]</a></td><td><a href='loadhero.php?heroid=$heroesid[4]'>$heroesname[4]</a></td><td><a href='loadhero.php?heroid=$heroesid[5]'>$heroesname[5]</a></td></tr>";
echo "</table>";

?>