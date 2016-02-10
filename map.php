<?php
  $length = 10;
  $width = 10;
  if(!empty($_GET['length'])) $length = $_GET['length'];
  if(!empty($_GET['width'])) $width = $_GET['width'];
  for($i = 0; $i<$width; $i++) {
  	for($j = 0; $j<$length; $j++) {
      echo "<img src='tile_216.gif'>";
  	}
  	echo "<br>";
  }
?>