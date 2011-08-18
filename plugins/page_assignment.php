<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");

$assignment = array();
if (!empty($_GET['id']))
{
	$query = "SELECT * FROM ".$db->prefix."assignments WHERE id='".intval($_GET['id'])."'";
	$result = $db->query($query) or displayError("Query", $db->error());
	if ($db->num_rows($result) > 0)
	{
			$assignment  = $db->fetch_assoc($result);
			$assignment['content'] = file_get_contents("templates/content/assignment_" . $assignment['id'] .".htm");		
	}
}
$render->assign("assignment",$assignment);
?>
