<script>
function updateReport(reportid) {
  document.getElementById('reportid').value = reportid;
  document.getElementById('reportfrm').submit();
}
</script>

<?php

include "checklogin.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
//$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
//$party = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party WHERE id = '$hero[party]'"));
$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE heroid = '$cookie[0]' AND Hero.heroparty = Party.partyid"));
$reports = mysqli_query($conn,"SELECT * FROM Reports WHERE party = '$hero[heroparty]' ORDER BY reportid DESC");

echo "Reports:<br>";

echo "<table><tr><th>Timestamp</th><th>Dungeon</th><th>Report</th></tr>";
while($row = mysqli_fetch_assoc($reports)) {
  echo "<tr><td>" . $row['timestamp'] . "</td><td>" . $row['dungeon'] . "</td><td><a href='javascript:updateReport(" . $row['reportid'] . ");'>Read</a></td></tr>";
}
echo "</table>";

mysqli_close($conn);

echo "<form name='reportfrm' id='reportfrm' method='GET' action='readreport.php'><input name='reportid' type='hidden' value='' id='reportid'></form>";

?>