<?php
global $db;
global $config;
global $render;
$query = "SELECT * FROM ".$db->prefix."assignments WHERE `release`='1' ORDER BY id ASC";
$result = $db->query($query);
$assignments = array();

if ($db->num_rows($result) > 0)
{
	while ($row = $db->fetch_assoc($result))
	{
		$dateinfo = strptime($row['date'],"%m/%d/%Y");
		$time = mktime(1,1,1,intval($dateinfo['tm_mon'])+1,$dateinfo['tm_mday'],$dateinfo['tm_year']+1900);
		$month = date("F", $time);
		$day = date("j",$time);
		$row['month'] = $month;
		
		$row['day'] = $day;

		$assignmentcontent = file_get_contents("templates/content/assignment_" . $row['id'] .".htm");		
		if (strpos($assignmentcontent, "http://") === 0 || strpos($assignmentcontent, "https://") === 0)
		{
			$row['url'] = true;
			$row['content'] = $assignmentcontent;
		}
		$assignments[] = $row;
		
	}


		
}
$render->assign("assignments",$assignments);





?>
