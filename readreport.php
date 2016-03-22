<html>
<head><title>Battle</title></head>
<body>
<?php

include "checklogin.php";
include "menu.php";

ini_set("display_errors", 1);

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$reportid = mysqli_real_escape_string($conn, $_GET['reportid']);
//$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE id = '$cookie[0]' AND Hero.party = Party.partyid"));
$report = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Reports WHERE reportid = '$reportid' AND heroid = '$cookie[0]'"));

if($report == NULL) { echo "Report not found"; }

// echo stripslashes($report);

mysqli_close($conn);

$reportintro = explode("|",stripslashes($report['reportintro']));
$reportinitiative = explode("|",stripslashes($report['reportinitiative']));
$reportmap = explode("|",stripslashes($report['reportmap']));
$reporttext = explode("|",stripslashes($report['reporttext']));

?>

<table style="width:100%;">
	<tr><td colspan="3" style="width:100%;text-align:center;height:28px;"><div id="reportintro" class="parchment"></div></td></tr>
	<tr>
		<td style="width:33%;text-align:center;padding:25px;" class="parchment"><div id="reportinitiative"></div></td>
		<td style="width:33%;text-align:center;padding:25px;" class="parchment"><div id="reportmap"></div></td>
		<td style="width:33%;text-align:center;line-height:22px;padding:25px;" class="parchment"><div id="reporttext"></div></td>
	</tr>
</table>
<br>

<center><button class="button" onclick="back();">Back</button><button class="button" onclick="next();" style="margin-left:100px;">Next</button></center>

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