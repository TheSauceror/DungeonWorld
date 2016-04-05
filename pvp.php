<?php

include "functions.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

$heroes = mysqli_query($conn,"SELECT * FROM Hero");

while($row = mysqli_fetch_assoc($heroes)) {
	//mysqli_query($conn, "UPDATE Hero SET maxgold = gold WHERE id = '$row[id]'");
}

mysqli_close($conn);

?>