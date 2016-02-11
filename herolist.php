<?php

include "checklogin.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
//$heroes = mysqli_query($conn,"SELECT * FROM Hero");
$heroes = mysqli_query($conn,"SELECT * FROM Hero, Party WHERE Hero.party = Party.partyid");

echo "Heroes:<br>";

echo "<table><tr><th>Name</th><th>Password</th><th>Race</th><th>Profession</th><th>Experience</th><th>Party</th><th>Max HP</th><th>Max MP</th><th>Initiative</th><th>Strength</th><th>Vitality</th><th>Dexterity</th><th>Intelligence</th><th>Piety</th></tr>";
while($row = mysqli_fetch_assoc($heroes)) {
  echo "<tr><td><a href='loadhero.php?id=" . $row['id'] . "'>" . $row['name'] . "</a></td><td>" . $row['pw'] . "</td><td>" . $row['race'] . "</td><td>" . $row['prof'] . "</td><td>" . $row['xp'] . "</td><td><a href='loadparty.php?partyid=" . $row['partyid'] . "'>" . $row['partyname'] . "</a></td><td>" . $row['maxhp'] . "</td><td>" . $row['maxmp'] . "</td><td>" . $row['initiative'] . "</td><td>" . $row['str'] . "</td><td>" . $row['vit'] . "</td><td>" . $row['dex'] . "</td><td>" . $row['nce'] . "</td><td>" . $row['pie'] . "</td></tr>";
}
echo "</table>";

mysqli_close($conn);
?>