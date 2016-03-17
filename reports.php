<script>
function updateReport(reportid) {
  document.getElementById('reportid').value = reportid;
  document.getElementById('reportfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
//$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
//$party = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party WHERE id = '$hero[party]'"));
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE id = '$cookie[0]' AND Hero.party = Party.partyid"));

$reports = mysqli_query($conn,"SELECT * FROM Reports WHERE party = '$hero[party]' ORDER BY reportid DESC LIMIT 15");

echo "Reports:<br>";

echo "<table class='parchment'><tr><th>Timestamp</th><th>Dungeon</th><th>Report</th></tr>";
while($row = mysqli_fetch_assoc($reports)) {
  echo "<tr><td>" . date("m-d-y H:i:s", $row['timestamp']) . "</td><td>" . $row['dungeon'] . "</td>";
  if($row['timestamp'] <= time()) {
  	echo "<td><a href='javascript:updateReport(" . $row['reportid'] . ");'>Read</a>"; //need to fix f12ing to other reports
  } else {
  	echo "<td>Currently exploring</td>";
  }
  echo "</td></tr>";
}
echo "</table>";

mysqli_close($conn);

echo "<form name='reportfrm' id='reportfrm' method='GET' action='readreport.php'><input name='reportid' type='hidden' value='' id='reportid'></form>";

?>