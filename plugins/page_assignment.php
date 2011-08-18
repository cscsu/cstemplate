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
					$dateinfo = strptime($assignment['date'],"%m/%d/%Y");
		$time = mktime(1,1,1,intval($dateinfo['tm_mon'])+1,$dateinfo['tm_mday'],$dateinfo['tm_year']+1900);
		$month = date("F", $time);
		$day = date("j",$time) - $dateinfo['tm_wday'];
		$assignment['month'] = $month;
		$assignment['day'] = $day;
	}
}
$render->assign("assignment",$assignment);
?>
