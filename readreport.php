<html>
<head><title>Battle</title></head>
<body>
<?php

include "checklogin.php";

ini_set("display_errors", 1);

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$reportid = mysqli_real_escape_string($conn, $_GET['reportid']);
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE id = '$cookie[0]' AND Hero.party = Party.partyid"));
//$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
//$party = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party WHERE id = '$hero[party]'"));
$report = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Reports WHERE reportid = '$reportid' AND party = '$hero[party]'"))['report'];

if($report == "") { echo "Report not found"; }

// echo stripslashes($report);

mysqli_close($conn);

$report = stripslashes($report);

$report = explode("|",$report);

?>

<button onclick="prev();">Previous</button><button onclick="next();">Next</button><br>
<div id="showarea"></div>

<script>
var report = <?php echo json_encode($report); ?>;
var index = 0;
document.getElementById("showarea").innerHTML = report[0];

function next() {
  if(index == report.length - 1) { return; }
  index++;
  document.getElementById("showarea").innerHTML = report[index];
}

function prev() {
  if(index == 0) { return; }
  index--;
  document.getElementById("showarea").innerHTML = report[index];
}

</script>

</body>
</html>