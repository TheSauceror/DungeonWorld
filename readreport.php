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
$report = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Reports WHERE reportid = '$reportid' AND party = '$hero[party]'"));

if($report == NULL) { echo "Report not found"; }

// echo stripslashes($report);

mysqli_close($conn);

$reportintro = explode("|",stripslashes($report['reportintro']));
$reportinitiative = explode("|",stripslashes($report['reportinitiative']));
$reportmap = explode("|",stripslashes($report['reportmap']));
$reporttext = explode("|",stripslashes($report['reporttext']));

?>

<div id="reportintro" style="background-color:red;"></div>
<table style="width:100%;">
	<tr>
		<td style="background-color:blue;width:33%;text-align:center;"><div id="reportinitiative"></div></td>
		<td style="background-color:green;width:33%;text-align:center;"><div id="reportmap"></div></td>
		<td style="background-color:yellow;width:33%;text-align:center;"><div id="reporttext"></div></td>
	</tr>
</table>
<br><br>

<center><button onclick="back();">Back</button><button onclick="next();" style="margin-left:100px;">Next</button></center>

<script>
var reportintro = <?php echo json_encode($reportintro); ?>;
var reportinitiative = <?php echo json_encode($reportinitiative); ?>;
var reportmap = <?php echo json_encode($reportmap); ?>;
var reporttext = <?php echo json_encode($reporttext); ?>;

var index = 0;

document.getElementById("reportintro").innerHTML = reportintro[0];
document.getElementById("reportinitiative").innerHTML = reportinitiative[0];
document.getElementById("reportmap").innerHTML = reportmap[0];
document.getElementById("reporttext").innerHTML = reporttext[0];

function next() {
  if(index == reportmap.length - 1) { return; }
  index++;
  document.getElementById("reportintro").innerHTML = reportintro[index];
  document.getElementById("reportinitiative").innerHTML = reportinitiative[index];
  document.getElementById("reportmap").innerHTML = reportmap[index];
  document.getElementById("reporttext").innerHTML = reporttext[index];
}

function back() {
  if(index == 0) { return; }
  index--;
  document.getElementById("reportintro").innerHTML = reportintro[index];
  document.getElementById("reportinitiative").innerHTML = reportinitiative[index];
  document.getElementById("reportmap").innerHTML = reportmap[index];
  document.getElementById("reporttext").innerHTML = reporttext[index];
}

</script>

</body>
</html>