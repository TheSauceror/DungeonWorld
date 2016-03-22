<?php
  include "functions.php";


$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

$name = 'test';
$id = 0;

//mysqli_query($conn,"INSERT INTO Guilds (guildname, owner, guilddes) VALUES ('$name', '$id','')") or die(mysqli_error($conn));

$newguild = mysqli_insert_id($conn);

print_r($newguild);
echo $newguild;

mysqli_close($conn);


?>