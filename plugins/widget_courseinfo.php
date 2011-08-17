<?php
global $db;
global $config;
global $render;
$query = "SELECT * FROM ".$db->prefix."instructors";
$result = $db->query($query);
$instructors = array();
$gtas = array();
if ($db->num_rows($result) > 0)
{
	while ($row = $db->fetch_assoc($result))
	{
		
		$hoursq = "SELECT * FROM ".$db->prefix."hours WHERE id='".intval($row['id'])."' AND type=0";
		$hresult = $db->query($hoursq);
		
		$officehours = array();
	
		while($hr = $db->fetch_assoc($hresult))
		{
			$officehours[] = $hr;
		}		
		$row['officehours'] = $officehours;
		if (intval($row['ta']) == 1)
			$gtas[] = $row;
		else
			$instructors[] = $row;
		
	}
	
}

$query = "SELECT * FROM ".$db->prefix."sections";
$result = $db->query($query);
$sections = array();
if ($db->num_rows($result) > 0)
{
	while ($row = $db->fetch_assoc($result))
	{
		
		$hoursq = "SELECT * FROM ".$db->prefix."hours WHERE sid='".intval($row['id'])."' AND type>0";
		$hresult = $db->query($hoursq);
		
		$lectures = array();
		$labs = array();
		$recits = array();
	
		while($hr = $db->fetch_assoc($hresult))
		{
			switch (intval($hr['type']))
			{
				case 1:
					$lectures[] = $hr;
					break;
				case 2:
					$labs[] = $hr;
					break;
				case 3:
					$recits[] = $hr;
					break;
			}
		}		
		$row['lectures'] = $lectures;
		$row['labs'] = $labs;
		$row['recits'] = $recits;

		$sections[] = $row;
		
	}
	
}


$render->assign("instructors",$instructors);
$render->assign("gtas",$gtas);
$render->assign("sections",$sections);




?>
