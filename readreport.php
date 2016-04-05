<html>
<head><title>Adventures Of Eld - Report</title></head>
<body>
<?php

include "checklogin.php";
include "menu.php";

ini_set("display_errors", 1);

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if($_POST['reportid'] == NULL) { header('Location: reports.php'); }

$reportid = mysqli_real_escape_string($conn, $_POST['reportid']);
$end = mysqli_real_escape_string($conn, $_POST['end']);

//$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE id = '$cookie[0]' AND Hero.party = Party.partyid"));
$report = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Reports WHERE reportid = '$reportid' AND heroid = '$cookie[0]'"));

// echo stripslashes($report);
mysqli_query($conn,"UPDATE Reports SET unread = 0 WHERE reportid = '$reportid' and heroid = '$cookie[0]'");

mysqli_close($conn);

$reportintro = explode("|",stripslashes($report['reportintro']));
$reportinitiative = explode("|",stripslashes($report['reportinitiative']));
$reportmap = explode("|",stripslashes($report['reportmap']));
$reporttext = explode("|",stripslashes($report['reporttext']));

?>

<table style="width:100%;">
	<tr><td colspan="3" style="height:28px;"><div id="reportintro" class="parchment center"></div></td></tr>
	<tr>
		<td style="padding:25px;" class="parchment width33 center"  id="reportinitiativediv"><div id="reportinitiative"></div></td>
		<td style="padding:25px;" class="parchment width33 center" id="reportmapdiv"><div id="reportmap"></div></td>
		<td style="line-height:22px;padding:25px;" class="parchment width33 center" id="reporttextdiv"><div id="reporttext"></div></td>
	</tr>
</table>
<br>

<center><button class="button hidden" id="back" onclick="back();">Back</button><button class="button" id="next" onclick="next();" style="margin-left:100px;">Next</button></center>

<script>
var reportintro = <?php echo json_encode($reportintro); ?>;
var reportinitiative = <?php echo json_encode($reportinitiative); ?>;
var reportmap = <?php echo json_encode($reportmap); ?>;
var reporttext = <?php echo json_encode($reporttext); ?>;

var index = 0;

if(<?php echo json_encode($end); ?> == 1) {
  index = reportmap.length - 1;
}

document.getElementById("reportintro").innerHTML = reportintro[index];
document.getElementById("reportinitiative").innerHTML = reportinitiative[index];
document.getElementById("reportmap").innerHTML = reportmap[index];
document.getElementById("reporttext").innerHTML = reporttext[index];

checkEmpty();

function next() {
  index++;
  document.getElementById("back").style.visibility = "visible";
  document.getElementById("reportintro").innerHTML = reportintro[index];
  document.getElementById("reportinitiative").innerHTML = reportinitiative[index];
  document.getElementById("reportmap").innerHTML = reportmap[index];
  document.getElementById("reporttext").innerHTML = reporttext[index];
  checkEmpty();
}

function back() {
  index--;
  document.getElementById("next").style.visibility = "visible";
  document.getElementById("reportintro").innerHTML = reportintro[index];
  document.getElementById("reportinitiative").innerHTML = reportinitiative[index];
  document.getElementById("reportmap").innerHTML = reportmap[index];
  document.getElementById("reporttext").innerHTML = reporttext[index];
  checkEmpty();
}

function checkEmpty() {
  if(index == 0) {
    document.getElementById("next").style.visibility = "visible";
    document.getElementById("back").style.visibility = "hidden";
  }
  if(index == reportmap.length - 1) {
    document.getElementById("next").style.visibility = "hidden";
    document.getElementById("back").style.visibility = "visible";
  }
  if(document.getElementById("reportintro").innerHTML == "") {
    document.getElementById("reportintro").style.visibility = "hidden";
  } else {
    document.getElementById("reportintro").style.visibility = "visible";
  }
  if(document.getElementById("reportinitiative").innerHTML == "") {
    document.getElementById("reportinitiativediv").style.visibility = "hidden";
  } else {
    document.getElementById("reportinitiativediv").style.visibility = "visible";
  }
  if(document.getElementById("reportmap").innerHTML == "") {
    document.getElementById("reportmapdiv").style.visibility = "hidden";
  } else {
    document.getElementById("reportmapdiv").style.visibility = "visible";
  }
  if(document.getElementById("reporttext").innerHTML == "") {
    document.getElementById("reporttextdiv").style.visibility = "hidden";
  } else {
    document.getElementById("reporttextdiv").style.visibility = "visible";
  }
}
</script>

</body>
</html>