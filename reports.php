<head><title>Adventures Of Eld - Reports</title></head>

<script>
function updateReport(reportid, end) {
  document.getElementById('reportid').value = reportid;
  document.getElementById('end').value = end;
  document.getElementById('reportfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
//$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
//$party = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party WHERE id = '$hero[party]'"));

if(strpos($hero['tutorial'], 'reportsintro') === false) {
  echo "<div class='alert'>No longer a rookie now, eh? Here's where you can check the <span class='red'>results</span> of your adventures. After you return from adventures, you'll see reports of how you faired. You can go through <span class='red'>room by room</span> and see how you did, or just skip to the end to check your <span class='red'>loot</span>. Buncha greedy bastards, the lot of ya! Use your gold to <a href='profile.php'><span class='red'>train</span></a>, or buy gear at the <a href='market.php'><span class='red'>Market</span></a>. Any items you find will go into your <a href='inventory.php'><span class='red'>Inventory</span></a>, and you can equip them in your <a href='profile.php'><span class='red'>Profile</span></a>.</div>";
  mysqli_query($conn,"UPDATE Hero SET tutorial = CONCAT(tutorial, 'reportsintro|') WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
}

$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE id = '$cookie[0]' AND Hero.party = Party.partyid"));
$reports = mysqli_query($conn,"SELECT * FROM Reports WHERE heroid = '$cookie[0]' ORDER BY reportid DESC LIMIT 15");

echo "<div class='parchment'><h3>Reports:</h3>";
echo "<table><tr><th>Timestamp</th><th>Dungeon</th><th>Report</th><th>Results</th></tr>";
while($row = mysqli_fetch_assoc($reports)) {
  echo "<tr><td>";
  if($row['unread'] == 1 && $row['timestamp'] <= time()) { echo "<strong>"; }
  echo date("m-d-y H:i:s", $row['timestamp']);
	if($row['unread'] == 1 && $row['timestamp'] <= time()) { echo "</strong>"; }
  echo "</td><td>";
  if($row['unread'] == 1 && $row['timestamp'] <= time()) { echo "<strong>"; }
  $color = "red";
  //if($row['victory'] == 1) { $color = "green"; }
  echo "<span class='red'>$row[dungeon]</span>";
  if($row['unread'] == 1 && $row['timestamp'] <= time()) { echo "</strong>"; }
  echo "</td><td>";
  if($row['timestamp'] <= time()) {
    if($row['unread'] == 1) { echo "<strong>"; }
    echo "<a href='javascript:updateReport(" . $row['reportid'] . ", 0);'>Read</a>"; //need to fix f12ing to other reports
    if($row['unread'] == 1) { echo "</strong>"; }
  } else {
    echo "Currently exploring";
  }
  if($row['unread'] == 1) { echo "</strong>"; }
  echo "</td><td>";
  if($row['timestamp'] <= time()) {
    if($row['unread'] == 1) { echo "<strong>"; }
    echo "<a href='javascript:updateReport(" . $row['reportid'] . ", 1);'>Results</a>"; //need to fix f12ing to other reports
    if($row['unread'] == 1) { echo "</strong>"; }
  } else {
    echo "Currently exploring";
  }
  if($row['unread'] == 1) { echo "</strong>"; }
  
  echo "</tr>";
}
echo "</table></div>";

mysqli_close($conn);

echo "<form name='reportfrm' id='reportfrm' method='POST' action='readreport.php'><input name='reportid' type='hidden' value='' id='reportid'><input name='end' type='hidden' value='0' id='end'></form>";

?>