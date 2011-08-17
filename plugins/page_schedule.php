<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");



if ($user->isGuest() != true)
{




	if (!empty($_POST['submit_add']))
	{

		$errors = doAddItem();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	if (!empty($_POST['submit_edit']))
	{
		

		$errors = doEditItem();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	if (!empty($_GET['edit']))
	{
		$render->assign("edit",true);
		$errors = doPreEditItem();

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

	if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['edit']))
	{
		$errors[] = "Invalid page name";


	}
	
	if (!empty($errors))

		return $errors;

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['edit'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such page exists";	
	}

	if (empty($errors))
	{
		
		$row = $db->fetch_assoc($result);
		$pagename = $row['name'];
		$pagetitle = $row['title'];
		$pagecontent = file_get_contents("templates/content/page_" . $fields['edit'] .".htm");
		$render->assign("editpage_pagename",$pagename);


		$render->assign("editpage_pagetitle",$pagetitle);


		$render->assign("editpage_pagecontent",$pagecontent);

		
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

	$fields = validateFields($_POST, array("pagename"=>true,"pagetitle"=>true,"pagecontent"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	$render->assign("editpage_pagename",$fields['pagename']);


	$render->assign("editpage_pagetitle",$fields['pagetitle']);


	$render->assign("editpage_pagecontent",$fields['pagecontent']);


	if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['pagename']))
	{
		$errors[] = "Invalid page name";


	}
	
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['pagename'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such page exists";	
	}

	if (empty($errors))
	{
		

		$query = "UPDATE ".$db->prefix."pages SET title='".$db->escape($fields['pagetitle'])."' WHERE name='".$db->escape($fields['pagename'])."'";
		$result = $db->query($query);

		file_put_contents("templates/content/page_" . $fields['pagename'] .".htm", $fields['pagecontent']);
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

	$fields = validateFields($_POST, array("itemdate"=>true,"materialstitle"=>false,"materialsresource"=>false, "readingstitle"=>false, "readingsresource"=>false,"itemnotes"=>false));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}



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
		
		for ($x = 0; $x < count($fields['materialstitle']); $x++)
		{
			$title = trim($fields['materialstitle'][$x]);
			$resource = trim($fields['materialsresource'][$x]);
			if ($title != "")
			{
				$t = array($title,$resource);
				$materials[] = $t;
			}
		}
		
		for ($x = 0; $x < count($fields['readingstitle']); $x++)
		{
			$title = trim($fields['readingstitle'][$x]);
			$resource = trim($fields['readingsresource'][$x]);
			if ($title != "")
			{
				$t = array($title,$resource);
				$readings[] = $t;
			}
		}

		print_r($materials);
		print_r($readings);

		$query = "INSERT INTO ".$db->prefix."schedule (date,notes) VALUES('".$db->escape($fields['itemdate'])."','".$db->escape($fields['itemnotes'])."')";
		$result = $db->query($query);
		return null;
	}
		
	return $errors;

}


?>