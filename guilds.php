<script>
function apply(applyid) {
  document.getElementById('applyid').value = applyid;
  document.getElementById('applyfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$guilds = mysqli_query($conn,"SELECT * FROM Guilds");

if(isset($_POST['applyid']) && $hero['guild'] == 0) {
	//protect these inputs from injection
  //protect other people items from f12ing
	$applyid = mysqli_real_escape_string($conn, $_POST['applyid']);
	mysqli_query($conn,"INSERT INTO GuildApplicants (heroid, guildid) VALUES ('$hero[id]', '$applyid')") or die(mysqli_error($conn));
	$guild = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Guilds WHERE guildid = '$applyid'")) or die(mysqli_error($conn));
  echo "You have applied to $guild[guildname]!";
}

if($hero['guild'] == 0) {
	echo "<form action='guild.php' method='post'><fieldset class='parchment'><legend>Create guild</legend>Name: <input type='text' name='guildname' required><input type='submit' value='Create'></fieldset></form>";
}

echo "Guilds:<br>";

echo "<table class='parchment'><tr><th>Name</th><th>Members</th><th>Description</th>";
if($hero['guild'] == 0) { echo "<th>Apply</th>"; }
echo "</tr>";
while($row = mysqli_fetch_assoc($guilds)) {
	$row['members'] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS members FROM Hero WHERE guild = '$row[guildid]'"))['members'];
  echo "<tr><td><a href='guild.php?id=" . $row['guildid'] . "'>" . $row['guildname'] . "</a></td><td>$row[members]</td><td>$row[guilddes]</td>";
  if($hero['guild'] == 0) {
  	if(is_null(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM GuildApplicants WHERE heroid = '$cookie[0]' AND guildid = $row[guildid]")))) {
  		echo "<td><a href='javascript:apply($row[guildid]);'>Apply</a></td>";
  	} else {
  		echo "<td>Applied!</td>";
  	}
  }
  echo "</tr>";
}
echo "</table>";
	
mysqli_close($conn);

echo "<form name='applyfrm' id='applyfrm' method='POST' action='guilds.php'><input name='applyid' type='hidden' value='' id='applyid'></form>";

?>