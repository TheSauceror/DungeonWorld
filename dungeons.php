<script>
function updateDungeon(dID) {
  document.getElementById('dungeonID').value = dID;
  document.getElementById('dungeonfrm').submit();
}
</script>

<?php

include "checklogin.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$dungeons = mysqli_query($conn,"SELECT * FROM Dungeons");

$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE heroid = '$cookie[0]' AND Hero.heroparty = Party.partyid"));

if($hero['heroparty'] == NULL) {
	echo "<center><h1>You need a party to do dungeons.</h1></center>";
	// exit;
}

if(strtotime($hero['cd']) > strtotime(date("m-d-y H:i:s"))) {
  echo "<center><h1>Dungeon cooldown until: " . $hero['cd'] . "</h1></center>";
  // exit;
}

echo "Dungeons:<br>";

echo "<table><tr><th>Name</th><th>Rooms</th></tr>";
while($row = mysqli_fetch_assoc($dungeons)) {
  echo "<tr><td><a href='javascript:updateDungeon(" . $row['dungeonid'] . ");'>" . $row['dungeonname'] . "</a></td><td>" . sizeof(explode("|",$row['rooms'])) . "</td></tr>";
}
echo "</table>";

echo "<form name='dungeonfrm' id='dungeonfrm' method='POST' action='battle.php'><input name='dungeon' type='hidden' value='' id='dungeonID'></form>";

mysqli_close($conn);
?>