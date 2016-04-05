<head><title>Adventures Of Eld - Message</title></head>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

if(isset($_POST['to'],$_POST['message'])) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $to = mysqli_real_escape_string($conn, $_POST['to']);
  $subject = mysqli_real_escape_string($conn, $_POST['subject']);
  $message = mysqli_real_escape_string($conn, $_POST['message']);
  mysqli_close($conn);
  sendMessage($to, $cookie[0], $subject, $message);
  echo "<div class='alert'>Message sent!</div>";
  echo '<script>setTimeout("self.close()", 1000 )</script>';
  exit();
}

$to = "";
$subject = "";
if(isset($_GET['to'])) { $to = $_GET['to']; }
if(isset($_GET['subject'])) { $subject = $_GET['subject']; }

echo "<div class='parchment'><form action='sendmessage.php' method='post'>
<h3>Send A Message</h3>
To: <input type='text' name='to' required value='$to'><br>
Subject: <input type='text' name='subject' required value='$subject'><br>
Message: <br><textarea name='message' required cols='40' rows='5'></textarea><br>
<input type='submit' value='Send'>
</form></div>";

?>