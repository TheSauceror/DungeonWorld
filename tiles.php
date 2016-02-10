<?php

ini_set("display_errors", 1);

echo "<h1>Tiles</h1>";

$files = array();
$path = "./images";
$c = 0;

if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;

        $file = "images/" . $file;

        $files[] = $file;

    }
    closedir($handle);
}

natsort($files);

foreach($files as $file) {
	echo "<img src='$file' title='$file'>";
    $c++;
	if($c == 25) { $c = 0; echo "<br>"; }
}

?>