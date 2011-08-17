<?php
global $db;
global $config;
global $render;
$query = "SELECT * FROM ".$db->prefix."schedule ORDER BY date ASC";
$result = $db->query($query);
$schedule = array();
$curweek = array();
$curweekend = -1;
if ($db->num_rows($result) > 0)
{
	while ($row = $db->fetch_assoc($result))
	{
		$dateinfo = strptime($row['date'],"%m/%d/%Y");
		$time = mktime(1,1,1,intval($dateinfo['tm_mon'])+1,$dateinfo['tm_mday'],$dateinfo['tm_year']+1900);
		$month = date("F", $time);
		$endmonth = date("F", $time);
		$maxdays = date("t", $time);
		$day = date("j",$time) - $dateinfo['tm_wday'];
		$endday = $day + 6;
		if ($endday > $maxdays)
		{
			$ntime = mktime(1,1,1,$dateinfo['tm_mon']+2,$dateinfo['tm_mday'],$dateinfo['tm_year']+1900);
			$endmonth = date("F", $ntime);
			$endday = $endday - $maxdays;
					
		}
		if (empty($curweek) || $curweek["title"] == "")
		{
			$curweek["title"] = "Week of " . $month . " " . $day . " to " . $endmonth . " " . $endday;
			$curweek['days'] = array();
			$curweekend = $endday;
		}
		if ($day > $curweekend)
		{
			$schedule[] = $curweek;
			$curweek = array();
			$curweek["title"] = "Week of " . $month . " " . $day . " to " . $endmonth . " " . $endday;
			$curweek['days'] = array();
			$curweekend = $endday;
		}
		


		$item = array();
		$item['day'] = date("l",$time);
		$item['id'] = intval($row['id']);
		
		$resources = "SELECT * FROM ".$db->prefix."resources WHERE sid=".intval($row['id'])." ORDER BY id ASC";
		$resresult = $db->query($resources);
		$materials = array();
		$readings = array();
		if ($db->num_rows($resresult) > 0)
		{
			while ($res = $db->fetch_assoc($resresult))
			{
				if (intval($res['type']) == 0)
				{
					$curmaterial = array();
					$curmaterial['title'] = $res['title'];
					$curmaterial['resource'] = $res['resource'];
					$materials[] = $curmaterial;
				}
				else
				{
					$curreading = array();
					$curreading['title'] = $res['title'];
					$curreading['resource'] = $res['resource'];
					$readings[] = $curreading;
				}
			}
		}
		$item['materials'] = $materials;
		$item['readings'] = $readings;
		$item['notes'] = $row['notes'];

		$curweek['days'][] = $item;
	}
	if (!empty($curweek))
	{
		$schedule[] = $curweek;
	}
}
$render->assign("schedule",$schedule);





?>
