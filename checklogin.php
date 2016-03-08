<head><link href="main.css" rel="stylesheet" type="text/css" /></head>

<?php

ini_set("display_errors", 1);

if(isset($_COOKIE["DungeonsOfEld"])) {
  $cookie = explode("||",$_COOKIE["DungeonsOfEld"]);
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
  mysqli_close($conn);
  if($cookie[1] == $hero['pw']) {
    return;
  } else {
    setcookie("DungeonsOfEld", "", time()-60*60*24*365);
    header('Location: login.php');
  }
}
else {
  header('Location: login.php');
}
?>