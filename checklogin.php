<head><link href="main.css" rel="stylesheet" type="text/css" /></head>

<?php

ini_set("display_errors", 1);

if(isset($_COOKIE["PHPRPG"])) {
  $cookie = explode("||",$_COOKIE["PHPRPG"]);
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
  if($cookie[1] == $hero['pw']) {
    echo "<a href='patchnotes.php'>Patch Notes</a> - ";
    echo "<a href='devtools.php'>Dev Tools</a> - ";
    echo "<a href='index.php'>" . $hero['name'] . "</a> - ";
    echo "<a href='attributes.php'>Attributes</a> - ";
    echo "<a href='herolist.php'>Hero List</a> - ";
    echo "<a href='loadhero.php'>Load Hero</a> - ";
    echo "<a href='loadparty.php'>Load Party</a> - ";
    echo "<a href='dungeons.php'>Dungeons</a> - ";
    echo "<a href='reports.php'>Reports</a> - ";
    echo "<a href='enemylist.php'>Enemy List</a> - ";
    echo "<a href='itemlist.php'>Item List</a> - ";
    echo "<a href='loaditem.php'>Load Item</a> - ";
    echo "<a href='skilllist.php'>Skill List</a> - ";
    echo "<a href='market.php'>Market</a> - ";
    echo "<a href='battleplan.php'>Battle Plan</a> - ";
    $result = mysqli_query($conn,"SELECT * FROM Messages WHERE receiver = '$cookie[0]' AND unread = 1");
    if(!is_null(mysqli_fetch_assoc($result))) { echo "<a href='messages.php'><strong>NEW MESSAGES (" . mysqli_num_rows($result) . ")</strong></a> - "; } else { echo "<a href='messages.php'>Messages</a> - "; };
    echo "<a href='logout.php'>Logout</a><br>";
    echo "<hr>";
    mysqli_close($conn);
    return;
  }
}
else {
  mysqli_close($conn);
  header('Location: login.php');
}
?>