<?php

include "checklogin.php";
include "menu.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(isset($_GET['delete'])) {
  $delete = mysqli_real_escape_string($conn, $_GET['delete']);
  mysqli_query($conn,"DELETE FROM Messages WHERE messageid = '$delete' AND receiver = '$cookie[0]'");
  echo "Message deleted!<br><br>";
}

if(isset($_GET['messageid'])) {
  $messageid = mysqli_real_escape_string($conn, $_GET['messageid']);
  //$readmessage = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Messages WHERE messageid = '$messageid' AND receiver = '$cookie[0]'"));
  $readmessage = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Messages WHERE messageid = '$messageid' AND receiver = '$cookie[0]' AND Hero.id = Messages.sender"));
  mysqli_query($conn,"UPDATE Messages SET unread = 0 WHERE messageid = '$messageid' and receiver = '$cookie[0]'");
  echo "<b>From:</b> " . $readmessage['name'] . "<br>";
  echo "<b>Subject:</b> " . $readmessage['subject'] . "<br>";
  echo "<b>Message:</b> " . $readmessage['message'] . "<br>";
  echo "<a href='sendmessage.php?to=$readmessage[sender]&subject=RE:" . $readmessage['subject'] . "' target='_blank'>Reply</a><br><br>";
  echo "<a href='messages.php?delete=$messageid'>Delete</a><br><br>";
  echo "<a href='messages.php'>Back</a>";
  exit();
}

$messages = mysqli_query($conn,"SELECT * FROM Hero, Messages WHERE receiver = '$cookie[0]' AND Hero.id = Messages.sender ORDER BY messageid DESC");

echo "<table class='parchment'><tr><th>From</th><th>Subject</th><th>Time</th></tr>";
while($row = mysqli_fetch_assoc($messages)) {
  echo "<tr><td>";
  if($row['unread']==1) { echo "<b>"; }
  echo "<a href='profile.php?id=" . $row['id'] . "'>" . $row['name'] . "</a>";
  if($row['unread']==1) { echo "</b>"; }
  echo "</td><td>";
  if($row['unread']==1) { echo "<b>"; }
  echo "<a href='messages.php?messageid=" . $row['messageid'] . "'>" . $row['subject'] . "</a>";
  if($row['unread']==1) { echo "</b>"; }
  echo "</td><td>";
  if($row['unread']==1) { echo "<b>"; }
  echo $row['timestamp'];
  if($row['unread']==1) { echo "</b>"; }
  echo "</td></tr>";
}
echo "</table>";

mysqli_close($conn);

?>