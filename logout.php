<?php
	setcookie("DungeonsOfEld", "", time()-60*60*24*365);
	echo "<META http-equiv='refresh' content='0;URL=login.php'>";
?>