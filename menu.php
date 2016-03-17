<?php

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));

echo "<a href='devtools.php' class='white'>Dev Tools</a> - ";
echo "<a href='profile.php' class='white'>" . $hero['name'] . "</a> - ";
echo "<a href='skills.php' class='white'>Skills</a> - ";
echo "<a href='inventory.php' class='white'>Inventory</a> - ";
echo "<a href='battleplan.php' class='white'>Battle Plan</a> - ";
echo "<a href='dungeons.php' class='white'>Dungeons</a> - ";
echo "<a href='reports.php' class='white'>Reports</a> - ";
echo "<a href='market.php' class='white'>Market</a> - ";
$result = mysqli_query($conn,"SELECT * FROM Messages WHERE receiver = '$cookie[0]' AND unread = 1");
if(!is_null(mysqli_fetch_assoc($result))) { echo "<a href='messages.php' class='white'><strong>NEW MESSAGES (" . mysqli_num_rows($result) . ")</strong></a> - "; } else { echo "<a href='messages.php' class='white'>Messages</a> - "; };
echo "<a href='patchnotes.php' class='white'>Patch Notes</a> - ";
echo "<a href='logout.php' class='white'>Logout</a><br>";
echo "<hr>";

mysqli_close($conn);

?>