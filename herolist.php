<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
//$heroes = mysqli_query($conn,"SELECT * FROM Hero");
$heroes = mysqli_query($conn,"SELECT * FROM Hero LEFT JOIN Guilds ON Hero.guild = Guilds.guildid ORDER BY id");

echo "<div class='parchment'><h3>Heroes:</h3>";

echo "<table><tr><th>Name</th><th>Race</th><th>Profession</th><th>Gold</th><th>Guild</th><th>Max HP</th><th>Max MP</th><th>Initiative</th><th>Strength</th><th>Vitality</th><th>Dexterity</th><th>Intelligence</th><th>Piety</th></tr>";
while($row = mysqli_fetch_assoc($heroes)) {
  echo "<tr><td><a href='profile.php?id=" . $row['id'] . "'>" . $row['name'] . "</a></td><td>" . $row['race'] . "</td><td>" . $row['prof'] . "</td><td>" . $row['gold'] . "</td><td><a href='guild.php?id=" . $row['guildid'] . "'>" . $row['guildname'] . "</a></td><td>" . $row['maxhp'] . "</td><td>" . $row['maxmp'] . "</td><td>" . $row['initiative'] . "</td><td>" . $row['str'] . "</td><td>" . $row['vit'] . "</td><td>" . $row['dex'] . "</td><td>" . $row['nce'] . "</td><td>" . $row['pie'] . "</td></tr>";
}
echo "</table></div>";

mysqli_close($conn);
?>