<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");



if ($user->isGuest() != true)
{


	$instructor_name = array();
	$instructor_url = array();
	$instructor_office = array();
	$instructor_email = array();
	$instructor_officehours_day = array();
	$instructor_officehours_start = array();
	$instructor_officehours_end = array();

	$ta_name = array();
	$ta_url = array();
	$ta_office = array();
	$ta_email = array();
	$ta_officehours_day = array();
	$ta_officehours_start = array();
	$ta_officehours_end = array();

	$section_number = array();
	$section_lecture_day = array();
	$section_lecture_start = array();
	$section_lecture_end = array();
	$section_lecture_location = array();

	$section_lab_day = array();
	$section_lab_start = array();
	$section_lab_end = array();
	$section_lab_location = array();

	$section_recit_day = array();
	$section_recit_start = array();
	$section_recit_end = array();
	$section_recit_location = array();

	$in_count = 0;
	$ta_count = 0;
	$sec_count = 0;

	$query = "SELECT * FROM ".$db->prefix."instructors";
	$result = $db->query($query);
	$instructors = array();
	$gtas = array();
	if ($db->num_rows($result) > 0)
	{
		while ($row = $db->fetch_assoc($result))
		{
		
			$hoursq = "SELECT * FROM ".$db->prefix."hours WHERE sid='".intval($row['id'])."' AND type=0";
			$hresult = $db->query($hoursq);
			if (intval($row['ta']) == 1)
			{
				$ta_name[$ta_count] = $row['name'];
				$ta_url[$ta_count] = $row['url'];
				$ta_email[$ta_count] = $row['email'];
				$ta_office[$ta_count] = $row['office'];
				if ($db->num_rows($hresult) > 0)
				{
					while($hr = $db->fetch_assoc($hresult))
					{
						$ta_officehours_day[$ta_count][] = $hr['day'];
						$ta_officehours_start[$ta_count][]  = $hr['start'];
						$ta_officehours_end[$ta_count][]  = $hr['end'];
					}
				}
				else
				{
						$ta_officehours_day[$ta_count][] = "";
						$ta_officehours_start[$ta_count][]  = "";
						$ta_officehours_end[$ta_count][]  = "";
				}
				$ta_count++;	
				
			}
			else
			{
				$instructor_name[$in_count] = $row['name'];
				$instructor_url[$in_count] = $row['url'];
				$instructor_email[$in_count] = $row['email'];
				$instructor_office[$in_count] = $row['office'];
				if ($db->num_rows($hresult) > 0)
				{
					while($hr = $db->fetch_assoc($hresult))
					{
						$instructor_officehours_day[$in_count][] = $hr['day'];
						$instructor_officehours_start[$in_count][]  = $hr['start'];
						$instructor_officehours_end[$in_count][]  = $hr['end'];
					}
				}
				else
				{
						$instructor_officehours_day[$in_count][] = "";
						$instructor_officehours_start[$in_count][]  = "";
						$instructor_officehours_end[$in_count][]  = "";
				}
				$in_count++;
			}
			
		
		}

			
	}

	$query = "SELECT * FROM ".$db->prefix."sections";
	$result = $db->query($query);
	if ($db->num_rows($result) > 0)
	{
		while ($row = $db->fetch_assoc($result))
		{
		
			$hoursq = "SELECT * FROM ".$db->prefix."hours WHERE sid='".intval($row['id'])."' AND type>0";
			$hresult = $db->query($hoursq);
			$section_number[$sec_count] = $row['number'];
			while($hr = $db->fetch_assoc($hresult))
			{
				switch (intval($hr['type']))
				{
					case 1:
						$section_lecture_day[$sec_count][] = $hr['day'];
						$section_lecture_location[$sec_count][]  = $hr['location'];
						$section_lecture_start[$sec_count][]  = $hr['start'];
						$section_lecture_end[$sec_count][]  = $hr['end'];
						break;
					case 2:
						$section_lab_day[$sec_count][] = $hr['day'];
						$section_lab_location[$sec_count][]  = $hr['location'];
						$section_lab_start[$sec_count][]  = $hr['start'];
						$section_lab_end[$sec_count][]  = $hr['end'];
						break;
					case 3:
						$section_recit_day[$sec_count][] = $hr['day'];
						$section_recit_location[$sec_count][]  = $hr['location'];
						$section_recit_start[$sec_count][]  = $hr['start'];
						$section_recit_end[$sec_count][]  = $hr['end'];
						break;
				}

			}

			if (count($section_lecture_day[$sec_count]) == 0)
			{
					$section_lecture_day[$sec_count][] = "";
					$section_lecture_location[$sec_count][]  = "";
					$section_lecture_start[$sec_count][]  = "";
					$section_lecture_end[$sec_count][]  = "";
			}

			if (count($section_lab_day[$sec_count]) == 0)
			{
					$section_lab_day[$sec_count][] = "";
					$section_lab_location[$sec_count][]  = "";
					$section_lab_start[$sec_count][]  = "";
					$section_lab_end[$sec_count][]  = "";
			}
			if (count($section_recit_day[$sec_count]) == 0)
			{
					$section_recit_day[$sec_count][] = "";
					$section_recit_location[$sec_count][]  = "";
					$section_recit_start[$sec_count][]  = "";
					$section_recit_end[$sec_count][]  = "";
			}
			$sec_count++;

		
		}

			
	}


	$render->assign("item_instructorname",$instructor_name);
	$render->assign("item_instructorurl",$instructor_url);
	$render->assign("item_instructoroffice",$instructor_office);
	$render->assign("item_instructoremail",$instructor_email);
	$render->assign("item_officehoursday",$instructor_officehours_day);
	$render->assign("item_officehoursstart",$instructor_officehours_start);
	$render->assign("item_officehoursend",$instructor_officehours_end);

	$render->assign("item_taname",$ta_name);
	$render->assign("item_taurl",$ta_url);
	$render->assign("item_taoffice",$ta_office);
	$render->assign("item_taemail",$ta_email);
	$render->assign("item_taofficehoursday",$ta_officehours_day);
	$render->assign("item_taofficehoursstart",$ta_officehours_start);
	$render->assign("item_taofficehoursend",$ta_officehours_end);

	$render->assign("item_sectionnumber",$section_number);
	$render->assign("item_lectureday",$section_lecture_day);
	$render->assign("item_lecturestart",$section_lecture_start);
	$render->assign("item_lectureend",$section_lecture_end);
	$render->assign("item_lecturelocation",$section_lecture_location);

	$render->assign("item_labday",$section_lab_day);
	$render->assign("item_labstart",$section_lab_start);
	$render->assign("item_labend",$section_lab_end);
	$render->assign("item_lablocation",$section_lab_location);

	$render->assign("item_recitday",$section_recit_day);
	$render->assign("item_recitstart",$section_recit_start);
	$render->assign("item_recitend",$section_recit_end);
	$render->assign("item_recitlocation",$section_recit_location);




	$render->assign("admin",1);

	if (!empty($_POST['submit_update']))
	{

		$errors = doUpdateCourse();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}
	
}
else
{
	redirect("You must be logged in");
}




function doUpdateCourse()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("instructor_name"=>false,
						"instructor_url"=>false,
						"instructor_office"=>false, 
						"instructor_email"=>false, 
						"instructor_iofficehours_day"=>false,
						"instructor_iofficehours_start"=>false,
						"instructor_iofficehours_end"=>false,
						"gta_name"=>false,
						"gta_url"=>false,
						"gta_office"=>false, 
						"gta_email"=>false, 
						"gta_tofficehours_day"=>false,
						"gta_tofficehours_start"=>false,
						"gta_tofficehours_end"=>false,
						"section_number"=>false,
						"section_lecture_day"=>false,
						"section_lecture_location"=>false,
						"section_lecture_start"=>false,
						"section_lecture_end"=>false,
						"section_lab_day"=>false,
						"section_lab_location"=>false,
						"section_lab_start"=>false,
						"section_lab_end"=>false,
						"section_recit_day"=>false,
						"section_recit_location"=>false,
						"section_recit_start"=>false,
						"section_recit_end"=>false));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	$render->assign("item_instructorname",$fields['instructor_name']);
	$render->assign("item_instructorurl",$fields['instructor_url']);
	$render->assign("item_instructoroffice",$fields['instructor_office']);
	$render->assign("item_instructoremail",$fields['instructor_email']);
	$render->assign("item_officehoursday",$fields['instructor_iofficehours_day']);
	$render->assign("item_officehoursstart",$fields['instructor_iofficehours_start']);
	$render->assign("item_officehoursend",$fields['instructor_iofficehours_end']);

	$render->assign("item_taname",$fields['gta_name']);
	$render->assign("item_taurl",$fields['gta_url']);
	$render->assign("item_taoffice",$fields['gta_office']);
	$render->assign("item_taemail",$fields['gta_email']);
	$render->assign("item_taofficehoursday",$fields['gta_tofficehours_day']);
	$render->assign("item_taofficehoursstart",$fields['gta_tofficehours_start']);
	$render->assign("item_taofficehoursend",$fields['gta_tofficehours_end']);

	$render->assign("item_sectionnumber",$fields['section_number']);
	$render->assign("item_lectureday",$fields['section_lecture_day']);
	$render->assign("item_lecturestart",$fields['section_lecture_start']);
	$render->assign("item_lectureend",$fields['section_lecture_end']);
	$render->assign("item_lecturelocation",$fields['section_lecture_location']);

	$render->assign("item_labday",$fields['section_lab_day']);
	$render->assign("item_labstart",$fields['section_lab_start']);
	$render->assign("item_labend",$fields['section_lab_end']);
	$render->assign("item_lablocation",$fields['section_lab_location']);

	$render->assign("item_recitday",$fields['section_recit_day']);
	$render->assign("item_recitstart",$fields['section_recit_start']);
	$render->assign("item_recitend",$fields['section_recit_end']);
	$render->assign("item_recitlocation",$fields['section_recit_location']);

	$query = "TRUNCATE TABLE ".$db->prefix."instructors";
	$result = $db->query($query);

	$query = "TRUNCATE TABLE ".$db->prefix."hours";
	$result = $db->query($query);

	$query = "TRUNCATE TABLE ".$db->prefix."sections";
	$result = $db->query($query);

	$hours = array();
		
		for ($x = 0; $x < count($fields['instructor_name']); $x++)
		{
			$name= trim($fields['instructor_name'][$x]);
			$url = trim($fields['instructor_url'][$x]);
			$email = trim($fields['instructor_email'][$x]);
			$office = trim($fields['instructor_office'][$x]);
			if ($name != "")
			{
				
				
				$query = "INSERT INTO ".$db->prefix."instructors (name,url,email,office) VALUES('".$db->escape($name)."','".$db->escape($url)."','".$db->escape($email)."','".$db->escape($office)."')";
				$result = $db->query($query);
				$sid = $db->insert_id();
				for ($y = 0; $y < count($fields['instructor_iofficehours_day'][$x]); $y++)
				{
					$day = $fields['instructor_iofficehours_day'][$x][$y];
					$start = $fields['instructor_iofficehours_start'][$x][$y];
					$end = $fields['instructor_iofficehours_end'][$x][$y];
					if (trim($start) != "" && trim($end) != "")
					{
						$h = array("sid"=>$sid,"day"=>$day,"start"=>$start,"end"=>$end,"type"=>0);
						$hours[] = $h;
					}
				}
			}
		}

		
		for ($x = 0; $x < count($fields['gta_name']); $x++)
		{
			$name= trim($fields['gta_name'][$x]);
			$url = trim($fields['gta_url'][$x]);
			$email = trim($fields['gta_email'][$x]);
			$office = trim($fields['gta_office'][$x]);
			if ($name != "")
			{
				
				
				$query = "INSERT INTO ".$db->prefix."instructors (name,url,email,office,ta) VALUES('".$db->escape($name)."','".$db->escape($url)."','".$db->escape($email)."','".$db->escape($office)."','1')";
				$result = $db->query($query);
				$sid = $db->insert_id();
				for ($y = 0; $y < count($fields['gta_tofficehours_day'][$x]); $y++)
				{
					$day = $fields['gta_tofficehours_day'][$x][$y];
					$start = $fields['gta_tofficehours_start'][$x][$y];
					$end = $fields['gta_tofficehours_end'][$x][$y];
					if (trim($start) != "" && trim($end) != "")
					{
						$h = array("sid"=>$sid,"day"=>$day,"start"=>$start,"end"=>$end,"type"=>0);
						$hours[] = $h;
					}
				}
			}
		}


		for ($x = 0; $x < count($fields['section_number']); $x++)
		{
			$number= trim($fields['section_number'][$x]);
			if ($name != "")
			{
				
				
				$query = "INSERT INTO ".$db->prefix."sections (number) VALUES('".$db->escape($number)."')";
				$result = $db->query($query);
				$sid = $db->insert_id();
				for ($y = 0; $y < count($fields['section_lecture_day'][$x]); $y++)
				{
					$day = $fields['section_lecture_day'][$x][$y];
					$location = $fields['section_lecture_location'][$x][$y];
					$start = $fields['section_lecture_start'][$x][$y];
					$end = $fields['section_lecture_end'][$x][$y];
					if (trim($start) != "" && trim($end) != "")
					{
						$h = array("sid"=>$sid, "day"=>$day, "start"=>$start,"end"=>$end, "location"=>$location, "type"=>1);
						$hours[] = $h;
					}
				}
				for ($y = 0; $y < count($fields['section_lab_day'][$x]); $y++)
				{
					$day = $fields['section_lab_day'][$x][$y];
					$location = $fields['section_lab_location'][$x][$y];
					$start = $fields['section_lab_start'][$x][$y];
					$end = $fields['section_lab_end'][$x][$y];
					if (trim($start) != "" && trim($end) != "")
					{
						$h = array("sid"=>$sid, "day"=>$day, "start"=>$start,"end"=>$end, "location"=>$location, "type"=>2);
						$hours[] = $h;
					}
				}
				for ($y = 0; $y < count($fields['section_recit_day'][$x]); $y++)
				{
					$day = $fields['section_recit_day'][$x][$y];
					$location = $fields['section_recit_location'][$x][$y];
					$start = $fields['section_recit_start'][$x][$y];
					$end = $fields['section_recit_end'][$x][$y];
					if (trim($start) != "" && trim($end) != "")
					{
						$h = array("sid"=>$sid, "day"=>$day, "start"=>$start,"end"=>$end, "location"=>$location, "type"=>3);
						$hours[] = $h;
					}
				}
			}
		}
			

		foreach ($hours as $h)
		{
			$query = "INSERT INTO ".$db->prefix."hours (sid,day,start,end,location,type) VALUES ('".intval($h['sid'])."','".$db->escape($h['day'])."','".$db->escape($h['start'])."','".$db->escape($h['end'])."','".$db->escape($h['location'])."','".intval($h['type'])."')";
			$db->query($query);
		}

		redirect("Course Information Updated","?page=course");
		
		return null;

}


?>
