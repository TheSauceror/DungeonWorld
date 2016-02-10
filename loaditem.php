<?php

include "checklogin.php";

$pre = "";
$base = "";
$suf = "";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
if(isset($_GET['pre'])) { $pre=mysqli_real_escape_string($conn, $_GET['pre']); }
if(isset($_GET['base'])) { $base=mysqli_real_escape_string($conn, $_GET['base']); }
if(isset($_GET['suf'])) { $suf=mysqli_real_escape_string($conn, $_GET['suf']); }
$itempre = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$pre' AND slot = 'prefix'"));
$itembase = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$base' AND slot != 'prefix' AND slot != 'suffix'"));
$itemsuf = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$suf' AND slot = 'suffix'"));
mysqli_close($conn);

$item[name] = trim($itempre[name] . " " . $itembase[name] . " " . $itemsuf[name]);
$item[slot] = $itembase[slot];
$item[sdam] = $itempre[sdam] + $itembase[sdam] + $itemsuf[sdam];
$item[pdam] = $itempre[pdam] + $itembase[pdam]+ $itemsuf[pdam];
$item[bdam] = $itempre[bdam] + $itembase[bdam] + $itemsuf[bdam];
$item[sarm] = $itempre[sarm] + $itembase[sarm] + $itemsuf[sarm];
$item[parm] = $itempre[parm] + $itembase[parm]+ $itemsuf[parm];
$item[barm] = $itempre[barm] + $itembase[barm] + $itemsuf[barm];
$item[hpreg] = $itempre[hpreg] + $itembase[hpreg] + $itemsuf[hpreg];
$item[mpreg] = $itempre[mpreg] + $itembase[mpreg] + $itemsuf[mpreg];
$item[des] = $itembase[des] . " " . $itempre[des] . " " . $itemsuf[des];

echo "<form name='itemsearch' action='loaditem.php' method='GET'><input type='text' name='pre' value='$pre'><input type='text' name='base' value='$base'><input type='text' name='suf' value='$suf'><input type='submit' value='Submit'></form>";

echo "Item:<br>";

echo "<table><tr><th>Name</th><th>Slot</th><th>Description</th><th>S. Dmg</th><th>P. Dmg</th><th>B. Dmg</th><th>S. Arm</th><th>P. Arm</th><th>B. Arm</th><th>HP Regen</th><th>MP Regen</th></tr>";
echo "<tr><td>" . $item['name'] . "</td><td>" . $item['slot'] . "</td><td>" . $item['des'] . "</td><td>" . $item['sdam'] . "</td><td>" . $item['pdam'] . "</td><td>" . $item['bdam'] . "</td><td>" . $item['sarm'] . "</td><td>" . $item['parm'] . "</td><td>" . $item['barm'] . "</td><td>" . $item['hpreg'] . "</td><td>" . $item['mpreg'] . "</td></tr>";
echo "</table>";

?>