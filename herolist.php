<head><title>Adventures Of Eld - Hero List</title></head>

<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

$heroes = mysqli_query($conn,"SELECT * FROM Hero LEFT JOIN Guilds ON Hero.guild = Guilds.guildid ORDER BY id");

echo "<div class='parchment'><h3>Heroes:</h3>";
echo "<table><tr><th>Name</th><th>Race</th><th>Profession</th><th>Guild</th><th>Max Gold</th><th>Dungeon Level</th></tr>";
while($row = mysqli_fetch_assoc($heroes)) {
  echo "<tr><td><a href='profile.php?id=" . $row['id'] . "'>" . $row['name'] . "</a></td><td>" . $row['race'] . "</td><td>" . $row['prof'] . "</td><td><a href='guild.php?id=" . $row['guildid'] . "'>" . $row['guildname'] . "</a></td><td class='center'>" . $row['maxgold'] . "</td><td class='center'>" . $row['dungeonlevel'] . "</td></tr>";
}
echo "</table></div>";

mysqli_close($conn);
?>