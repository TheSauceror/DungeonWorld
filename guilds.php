<head><title>Adventures Of Eld - Guilds</title></head>

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

if(strpos($hero['tutorial'], 'guildsintro') === false) {
  echo "<div class='alert'>Here's where you can find a list of all of the <span class='red'>guilds</span> around Eld. <span class='red'>Joining</span> a guild can be a great way to find adventurers with similar interests to party with. Or if you're more cut out for the leadership role, you can <span class='red'>create</span> your own guild.</div>";
  mysqli_query($conn,"UPDATE Hero SET tutorial = CONCAT(tutorial, 'guildsintro|') WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
}

if(isset($_POST['applyid']) && $hero['guild'] == 0) {
	//protect these inputs from injection
  //protect other people items from f12ing
	$applyid = mysqli_real_escape_string($conn, $_POST['applyid']);
	mysqli_query($conn,"INSERT INTO GuildApplicants (heroid, guildid) VALUES ('$hero[id]', '$applyid')") or die(mysqli_error($conn));
	$guild = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Guilds WHERE guildid = '$applyid'")) or die(mysqli_error($conn));
  sendMessage($guild['owner'], 0, $guild['guildname'], "$hero[name] has applied to $guild[guildname]!");
  echo "<div class='alert'>You have applied to $guild[guildname]!</div>";
}

if(isset($_POST['leaveid'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $leaveid = mysqli_real_escape_string($conn, $_POST['leaveid']);
  $guild = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Guilds WHERE guildid = '$hero[id]'")) or die(mysqli_error($conn));
  mysqli_query($conn, "UPDATE Hero SET guild = 0 WHERE id = '$leaveid'");
  sendMessage($guild['owner'], 0, $guild['guildname'], "$hero[name] has left $guild[guildname]!");
  echo "<div class='alert'>You have left $guild[guildname]!</div>";
  $hero['guild'] = 0;
}

if(isset($_POST['disbandid'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $disbandid = mysqli_real_escape_string($conn, $_POST['disbandid']);
  $guild = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Guilds WHERE guildid = '$hero[guild]'")) or die(mysqli_error($conn));
  mysqli_query($conn, "DELETE FROM Guilds WHERE guildid = '$hero[guild]'");
  mysqli_query($conn, "UPDATE Hero SET guild = 0 WHERE id = '$hero[id]'");
  echo "<div class='alert'>You have disbanded $guild[guildname]!</div>";
  $hero['guild'] = 0;
}

if($hero['guild'] == 0) {
	echo "<form action='guild.php' method='post'><div class='parchment'><h3>Create guild</h3>Name: <input type='text' name='guildname' required><input type='submit' value='Create'></form></div>";
}

$guilds = mysqli_query($conn,"SELECT * FROM Guilds");

echo "<div class='parchment'><h3>Guilds:</h3>";

echo "<table><tr><th>Name</th><th>Members</th><th>Description</th><th>Max Gold</th>";
if($hero['guild'] == 0) { echo "<th>Apply</th>"; }
echo "</tr>";
while($row = mysqli_fetch_assoc($guilds)) {
	$row['members'] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS members FROM Hero WHERE guild = '$row[guildid]'"))['members'];
  $row['maxgold'] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(maxgold) as maxgold FROM Hero WHERE guild = '$row[guildid]'"))['maxgold'];
  echo "<tr><td><a href='guild.php?id=" . $row['guildid'] . "'>" . $row['guildname'] . "</a></td><td>$row[members]</td><td>$row[guilddes]</td><td>$row[maxgold]</td>";
  if($hero['guild'] == 0) {
  	if(is_null(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM GuildApplicants WHERE heroid = '$cookie[0]' AND guildid = $row[guildid]")))) {
  		echo "<td><a href='javascript:apply($row[guildid]);'>Apply</a></td>";
  	} else {
  		echo "<td>Applied!</td>";
  	}
  }
  echo "</tr>";
}
echo "</table></div>";
	
mysqli_close($conn);

echo "<form name='applyfrm' id='applyfrm' method='POST' action='guilds.php'><input name='applyid' type='hidden' value='' id='applyid'></form>";

?>