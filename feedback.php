<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(isset($_POST['message'])) {
  //protect these inputs  from injection
  //protect other people items from f12ing!!!!!!!!!!!!!!!!
  $subject = mysqli_real_escape_string($conn, $_POST['subject']);
  $message = mysqli_real_escape_string($conn, $_POST['message']);
  mysqli_query($conn, "INSERT INTO Feedback (heroid, subject, message) VALUES ('$cookie[0]', '$subject', '$message')");
  echo "<div class='parchment'>Thank you for your feedback!</div>";
} else {
  echo "<div class='parchment'><h3>Feedback</h3>
<form action='feedback.php' method='POST'>
Subject: <input type='text' name='subject' required><br>
Message: <textarea name='message' rows='4' cols='50' required></textarea><br>
<input type='submit'>
</form></div>";
}

mysqli_close($conn);

?>