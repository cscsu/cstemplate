<?php
	global $db;
	global $render;
	$query = "SELECT url FROM ".$db->prefix."social WHERE name='facebook'";
	$result = $db->query($query);
	$row = $db->fetch_assoc($result);
	$render->assign("fbpageurl",$row['url']);

?>
