<?php

include "checklogin.php";
include "menu.php";

if(isset($_POST['to'],$_POST['message'])) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $to = mysqli_real_escape_string($conn, $_POST['to']);
  $subject = mysqli_real_escape_string($conn, $_POST['subject']);
  $message = mysqli_real_escape_string($conn, $_POST['message']);
  $timestamp = date("m-d-y H:i:s");
  mysqli_query($conn, "INSERT INTO Messages (receiver, sender, subject, message, timestamp) VALUES ('$to', '$cookie[0]', '$subject', '$message', '$timestamp')");
  mysqli_close($conn);
  echo "Message sent!";
  echo '<script>setTimeout("self.close()", 1000 )</script>';
  exit();
}

$to = "";
$subject = "";
if(isset($_GET['to'])) { $to = $_GET['to']; }
if(isset($_GET['subject'])) { $subject = $_GET['subject']; }

echo "<form action='sendmessage.php' method='post'>
<fieldset>
<legend>Send A Message</legend>
To: <input type='text' name='to' required value='$to'><br>
Subject: <input type='text' name='subject' required value='$subject'><br>
Message: <br><textarea name='message' required cols='40' rows='5'></textarea><br>
<input type='submit'>
</fieldset></form>";

?>