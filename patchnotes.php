<?php
	include "checklogin.php";
?>
<h2>Planned Updates</h2>
<ul>
	<li>Make an enemy editor form</li>
	<li>Make a room generator form</li>
	<li>Make a room editor form</li>
	<li>Make a dungeon generator form</li>
	<li>Make a item generator form</li>
	<li>Make a item editor form</li>
</ul>
<hr width='33%'>
<h2>Known Bugs</h2>
<ul>
	<li>Dungeons don't end on failure correctly</li>
	<li>Fix hero battleplan</li>
	<li>Fix market</li>
	<li>Need to move messages from GET to POST</li>
	<li>Need to fix sendmessage and replying</li>
</ul>
<hr width='33%'>
<h2>February 15, 2016</h2>
<ul>
	<li>Items show up again and add to stats correctly</li>
	<li>Herolist shows all heroes now</li>
</ul>
<hr width='33%'>
<h2>February 13, 2016</h2>
<ul>
	<li>Dungeons shouldn't  reach the max text size and freeze anymore</li>
	<li>Fixed intelligence</li>
	<li>Race/prof attribute %'s are precalculated before battle</li>
</ul>
<hr width='33%'>
<h2>February 11, 2016</h2>
<ul>
	<li>Started improving report UI</li>
	<li>Seperated battle report into divs</li>
	<li>Fixed report image titles</li>
</ul>
<h2>February 10, 2016</h2>
<ul>
	<li>Started changing database to get everything by ID instead of name</li>
	<li>Started crying 'cause I have to redo the entire database :(</li>
	<li>Ragequit 'cause I didn't realize I messed up halfway through redoing it all :(</li>
	<li>Changed attributes</li>
</ul>
<h2>February 9, 2016</h2>
<ul>
	<li>Fixed the turn order to read initiative from database</li>
	<li>***FIXED THE PARTY FROM REGAINING MAXHP EVERY ROOM***</li>
	<li>Made an enemy generator form</li>
</ul>
<h2>February 7, 2016</h2>
<ul>
	<li>Put maxhp/hp/maxmp/mp/initiative in enemy database</li>
	<li>Updated enemy search to pull maxhp/maxmp/initiative from database</li>
	<li>Updated enemy list with maxhp/maxmp/initiative</li>
	<li>Updated enemy database with maxhp/hp/maxmp/mp/initiative</li>
</ul>
<h2>February 5, 2016</h2>
<ul>
	<li>Put maxhp/hp/maxmp/mp/initiative in hero database</li>
	<li>Probably added them to character generation also. Don't think we can test it until items are fixed</li>
	<li>Updated hero search to pull maxhp/maxmp/initiative from database</li>
	<li>Updated hero list with maxhp/maxmp/initiative</li>
</ul>
<h2>February 2, 2016</h2>
<ul>
	<li>Changed dungeon cooldown to 10 minutes/room from 1 hour/room</li>
	<li>Added distinct floor options for rooms</li>
	<li>Fixed room counting in dungeons</li>
	<li>Added the Whiteshire Abbey dungeon</li>
</ul>
<h2>January 29, 2016</h2>
<ul>
	<li>Started recording patch notes</li>
	<li>Moved home.php to index.php</li>
	<li>Moved images to images folder</li>
	<li>Fixed image urls</li>
	<li>Fixed unit image settings to pull from profession instead of name</li>
</ul>