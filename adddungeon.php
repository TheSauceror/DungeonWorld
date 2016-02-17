<?php
include "checklogin.php";
?>

<?php
ini_set("display_errors", 1);

if(isset($_POST['name'])) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $rooms = mysqli_real_escape_string($conn, $_POST['rooms']);
  $des = mysqli_real_escape_string($conn, $_POST['des']);

  echo $name, $rooms, $des;

  // Needs to be updated with loot when loot is finished
  mysqli_query($conn, "INSERT INTO Dungeons (dungeonname, rooms, des, loot) VALUES ('$name', '$rooms', '$des', '')") or die (mysqli_error($conn));
  mysqli_close($conn);
  echo "Dungeon Added!";
}
?>

<h1>Add Dungeon</h1>
<form name='adddungeonfrm' id='dungeonfrm' method='POST' action='adddungeon.php'>
	<table>
	<tr><td>Name</td><td><input type='text' name='name' required></td></tr>
	<tr><td>Rooms (seperate with '|')</td><td><input type='text' name='rooms' required></td></tr>
	<tr><td>Description</td><td><textarea name='des' required></textarea></td></tr>
	</table>
	<br>
	<input type='submit' value='Add Dungeon'>
</form>
