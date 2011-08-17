<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");



if ($user->isGuest() != true)
{



	$render->assign("admin",1);

	if (!empty($_POST['submit_add']))
	{

		$errors = doAddItem();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_POST['submit_edit']))
	{
		
		$render->assign("edit",true);
		$errors = doEditItem();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_GET['edit']))
	{
		$render->assign("edit",true);
		$errors = doPreEditItem();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}
	else if (!empty($_GET['remove']))
	{
		$errors = doRemoveItem();

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


function doRemoveItem()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_GET, array("remove"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	if (intval($fields['remove']) <= 0)
		$errors[] = "Invalid item id";
	
	if (!empty($errors))

		return $errors;

	$query = "SELECT * FROM ".$db->prefix."schedule WHERE id='".intval($fields['remove'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "A entry with that id does not exist";	
	}


	if (empty($errors))
	{
		$query = "DELETE FROM ".$db->prefix."resources WHERE sid='".intval($fields['remove'])."'";
		$db->query($query);

		$query = "DELETE FROM ".$db->prefix."schedule WHERE id='".intval($fields['remove'])."'";
		$db->query($query);

	
		redirect("Schedule item deleted", "?page=schedule");
	
	}
	return $errors;
}


function doPreEditItem()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_GET, array("edit"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	if (intval($fields['edit']) <= 0)
		$errors[] = "Invalid item id";
	
	if (!empty($errors))

		return $errors;

	$query = "SELECT * FROM ".$db->prefix."schedule WHERE id='".intval($fields['edit'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "A entry with that id does not exist";	
	}


	if (empty($errors))
	{
		
		$row = $db->fetch_assoc($result);

		$render->assign("item_date", $row['date']);
		$render->assign("item_notes", $row['notes']);
		$render->assign("item_id", $row['id']);
		$resources = "SELECT * FROM ".$db->prefix."resources WHERE sid=".intval($fields['edit'])." ORDER BY id ASC";
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
					$curmaterial['id'] = $res['id'];
					$curmaterial['title'] = $res['title'];
					$curmaterial['resource'] = $res['resource'];
					$materials[] = $curmaterial;
				}
				else
				{
					$curreading = array();
					$curreading['id'] = $res['id'];
					$curreading['title'] = $res['title'];
					$curreading['resource'] = $res['resource'];
					$readings[] = $curreading;
				}
			}
		}
		$render->assign("item_materials", $materials);
		$render->assign("item_readings", $readings);
		return;
		
	}
		
	return $errors;

}


function doEditItem()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();


	$fields = validateFields($_POST, array("itemid"=>true,"itemdate"=>true,
						"materials_title"=>false,
						"materials_resource"=>false, 
						"readings_title"=>false, 
						"readings_resource"=>false,
						"materials_titleadd"=>false,
						"materials_resourceadd"=>false, 
						"readings_titleadd"=>false, 
						"readings_resourceadd"=>false,
						"itemnotes"=>false));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}
	$render->assign("item_id", $fields['itemid']);
	$render->assign("item_date",$fields['itemdate']);
	$render->assign("item_notes",$fields['itemnotes']);



	$materials = array();
	$readings = array();
		
		if (!empty($fields['materials_title']))
		{
			foreach ($fields['materials_title'] as $key => $value)
			{
				$id = $key;
				$title = trim($value);
				$resource = trim($fields['materials_resource'][$key]);
				if ($title != "")
				{
					$t = array("id"=>$id, "title"=>$title,"resource"=>$resource);
					$materials[] = $t;
				}
			}
		}
		
		if (!empty($fields['readings_title']))
		{
		foreach ($fields['readings_title'] as $key => $value)
		{
			$id = $key;
			$title = trim($value);
			$resource = trim($fields['readings_resource'][$key]);
			if ($title != "")
			{
				$t = array("id"=>$id, "title"=>$title,"resource"=>$resource);
				$readings[] = $t;
			}
		}
		}
		if (!empty($fields['materials_titleadd']))
		{
		foreach ($fields['materials_titleadd'] as $key => $value)
		{
			$id = $key;
			$title = trim($value);
			$resource = trim($fields['materials_resourceadd'][$key]);
			if ($title != "")
			{
				$t = array("id"=>$id, "title"=>$title,"resource"=>$resource);
				$materials[] = $t;
			}
		}
		}
		if (!empty($fields['readings_titleadd']))
		{
		foreach ($fields['readings_titleadd'] as $key => $value)
		{
			$id = $key;
			$title = trim($value);
			$resource = trim($fields['readings_resourceadd'][$key]);
			if ($title != "")
			{
				$t = array("id"=>$id, "title"=>$title,"resource"=>$resource);
				$readings[] = $t;
			}
		}
		}





	$render->assign("item_materials",$materials);
	$render->assign("item_readings",$readings);



	if (!preg_match("/^[0-1][0-9]\\/[0-3][0-9]\\/20[0-9][0-9]+$/i",$fields['itemdate']))
	{
		$errors[] = "Invalid date";


	}
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."schedule WHERE id='".intval($fields['itemid'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "A entry with that date does not exist";	
	}

	if (empty($errors))
	{



		$query = "UPDATE ".$db->prefix."schedule SET date='".$db->escape($fields['itemdate'])."', notes='".$db->escape($fields['itemnotes'])."' WHERE id='".intval($fields['itemid'])."'";
		$result = $db->query($query);		

		$query = "DELETE FROM ".$db->prefix."resources WHERE sid='".intval($fields['itemid'])."'";
		$result = $db->query($query);

		foreach ($materials as $mat)
		{
			$query = "INSERT INTO ".$db->prefix."resources (sid,title,resource,type) VALUES ('".intval($fields['itemid'])."','".$db->escape($mat['title'])."','".$db->escape($mat['resource'])."','0')";
			$db->query($query);
		}

		foreach ($readings as $read)
		{
			$query = "INSERT INTO ".$db->prefix."resources (sid,title,resource,type) VALUES ('".intval($fields['itemid'])."','".$db->escape($read['title'])."','".$db->escape($read['resource'])."','1')";
			$db->query($query);
		}
		redirect("Schedule item modified","?page=schedule");
		
		return null;
	}
		
	return $errors;

}

function doAddItem()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("itemdate"=>true,"materials_title"=>false,"materials_resource"=>false, "readings_title"=>false, "readings_resource"=>false,"itemnotes"=>false));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	$render->assign("item_date",$fields['itemdate']);
	$render->assign("item_notes",$fields['itemnotes']);
	$render->assign("item_materialstitle",$fields['materials_title']);
	$render->assign("item_materialsresource",$fields['materials_resource']);
	$render->assign("item_readingstitle",$fields['readings_title']);
	$render->assign("item_readingsresource",$fields['readings_resource']);

	if (!preg_match("/^[0-1][0-9]\\/[0-3][0-9]\\/20[0-9][0-9]+$/i",$fields['itemdate']))
	{
		$errors[] = "Invalid date";


	}
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."schedule WHERE date='".$db->escape($fields['itemdate'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) > 0)
	{
		$errors[] = "A entry with that date already exists";	
	}

	if (empty($errors))
	{
		$materials = array();
		$readings = array();
		
		for ($x = 0; $x < count($fields['materials_title']); $x++)
		{
			$title = trim($fields['materials_title'][$x]);
			$resource = trim($fields['materials_resource'][$x]);
			if ($title != "")
			{
				$t = array($title,$resource);
				$materials[] = $t;
			}
		}
		
		for ($x = 0; $x < count($fields['readings_title']); $x++)
		{
			$title = trim($fields['readings_title'][$x]);
			$resource = trim($fields['readings_resource'][$x]);
			if ($title != "")
			{
				$t = array($title,$resource);
				$readings[] = $t;
			}
		}

		$query = "INSERT INTO ".$db->prefix."schedule (date,notes) VALUES('".$db->escape($fields['itemdate'])."','".$db->escape($fields['itemnotes'])."')";
		$result = $db->query($query);
		$sid = $db->insert_id();
		

		foreach ($materials as $mat)
		{
			$query = "INSERT INTO ".$db->prefix."resources (sid,title,resource,type) VALUES ('".$sid."','".$db->escape($mat[0])."','".$db->escape($mat[1])."','0')";
			$db->query($query);
		}

		foreach ($readings as $read)
		{
			$query = "INSERT INTO ".$db->prefix."resources (sid,title,resource,type) VALUES ('".$sid."','".$db->escape($read[0])."','".$db->escape($read[1])."','1')";
			$db->query($query);
		}
		redirect("Schedule item added","?page=schedule");
		
		return null;
	}
		
	return $errors;

}


?>
