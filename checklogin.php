<head><link href="main.css" rel="stylesheet" type="text/css" /></head>

<?php

ini_set("display_errors", 1);

if(isset($_COOKIE["DungeonsOfEld"])) {
  $cookie = explode("||",$_COOKIE["DungeonsOfEld"]);
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
  if($cookie[1] == $hero['pw']) {
    echo "<a href='devtools.php'>Dev Tools</a> - ";
    echo "<a href='profile.php'>" . $hero['name'] . "</a> - ";
    echo "<a href='inventory.php'>Inventory</a> - ";
    echo "<a href='battleplan.php'>Battle Plan</a> - ";
    //echo "<a href='attributes.php'>Attributes</a> - ";
    //echo "<a href='loadhero.php'>Load Hero</a> - ";
    //echo "<a href='loadparty.php'>Load Party</a> - ";
    echo "<a href='dungeons.php'>Dungeons</a> - ";
    echo "<a href='reports.php'>Reports</a> - ";
    //echo "<a href='loaditem.php'>Load Item</a> - ";
    //echo "<a href='skilllist.php'>Skill List</a> - ";
    echo "<a href='market.php'>Market</a> - ";
    $result = mysqli_query($conn,"SELECT * FROM Messages WHERE receiver = '$cookie[0]' AND unread = 1");
    if(!is_null(mysqli_fetch_assoc($result))) { echo "<a href='messages.php'><strong>NEW MESSAGES (" . mysqli_num_rows($result) . ")</strong></a> - "; } else { echo "<a href='messages.php'>Messages</a> - "; };
    echo "<a href='patchnotes.php'>Patch Notes</a> - ";
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