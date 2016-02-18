<?php
include "checklogin.php";
?>

<?php
ini_set("display_errors", 1);

if(isset($_POST['length'])) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $length = mysqli_real_escape_string($conn, $_POST['length']);
  $width = mysqli_real_escape_string($conn, $_POST['width']);
  $floor = mysqli_real_escape_string($conn, $_POST['floor']);
  $enemies = mysqli_real_escape_string($conn, $_POST['enemies']);

  echo $length, $width, $floor, $enemies;

  mysqli_query($conn, "INSERT INTO Rooms (length, width, floor, enemies) VALUES ('$length', '$width', '$floor', '$enemies')") or die (mysqli_error($conn));
  mysqli_close($conn);
  echo "Room Added!";
}
?>

<h1>Add Room</h1>
<form name='addroomfrm' id='roomfrm' method='POST' action='addroom.php'>
	<table>
	<tr><td>Length</td><td><input type='number' name='length' required></td></tr>
	<tr><td>Width</td><td><input type='number' name='width' required></td></tr>
	<tr><td>Floor texture</td><td><input type='text' name='floor' required></td></tr>
	<tr><td>Enemies (separate with '|')</td><td><input type='text' name='enemies' required></td></tr>
	</table>
	<br>
	<input type='submit' value='Add Room'>
</form>
