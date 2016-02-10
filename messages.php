<?php

include "checklogin.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(isset($_GET['delete'])) {
  $delete = mysqli_real_escape_string($conn, $_GET['delete']);
  mysqli_query($conn,"DELETE FROM Messages WHERE id = '$delete' AND receiver = '$cookie[0]'");
  echo "Message deleted!<br><br>";
}

if(isset($_GET['read'])) {
  $read = mysqli_real_escape_string($conn, $_GET['read']);
  $readmessage = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Messages WHERE id = '$read' AND receiver = '$cookie[0]'"));
  mysqli_query($conn,"UPDATE Messages SET unread = 0 WHERE id = '$read' and receiver = '$cookie[0]'");
  echo "<b>From:</b> " . $readmessage[sender] . "<br>";
  echo "<b>Subject:</b> " . $readmessage[subject] . "<br>";
  echo "<b>Message:</b> " . $readmessage[message] . "<br>";
  echo "<a href='sendmessage.php?to=$readmessage[sender]&subject=RE:" . $readmessage[subject] . "' target='_blank'>Reply</a><br><br>";
  echo "<a href='messages.php?delete=$read'>Delete</a><br><br>";
  echo "<a href='messages.php'>Back</a>";
  exit();
}

$messages = mysqli_query($conn,"SELECT * FROM Messages WHERE receiver = '$cookie[0]'");

echo "<table><tr><th>From</th><th>Subject</th><th>Time</th></tr>";
while($row = mysqli_fetch_assoc($messages)) {
  echo "<tr><td>";
  if($row[unread]==1) { echo "<b>"; }
  echo "<a href='loadhero.php?searchname=" . $row[sender] . "'>" . $row[sender] . "</a>";
  if($row[unread]==1) { echo "</b>"; }
  echo "</td><td>";
  if($row[unread]==1) { echo "<b>"; }
  echo "<a href='messages.php?read=" . $row[id] . "'>" . $row[subject] . "</a>";
  if($row[unread]==1) { echo "</b>"; }
  echo "</td><td>";
  if($row[unread]==1) { echo "<b>"; }
  echo $row[timestamp];
  if($row[unread]==1) { echo "</b>"; }
  echo "</td></tr>";
}
echo "</table>";

mysqli_close($conn);

?>