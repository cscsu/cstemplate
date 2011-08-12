<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");


if ($user->isGuest() != true)
{


	$query = "SELECT * FROM ".$db->prefix."pages";
	$result = $db->query($query);
	$users = array();
	if ($db->num_rows($result) > 0)
	{
		
		while ($row = $db->fetch_assoc($result))
		{
			$pages[] = $row;
		}
	}
	$render->assign("pages",$pages);


	if (!empty($_POST['submit_add']))
	{

		$errors = doAddPage();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	if (!empty($_POST['submit_edit']))
	{
		

		$errors = doEditPage();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	if (!empty($_GET['edit']))
	{
		$render->assign("edit",true);
		$errors = doPreEditPage();

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


function doPreEditPage()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_GET, array("edit"=>true,));


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


function doEditPage()
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

function doAddPage()
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



	if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['pagename']))
	{
		$errors[] = "Invalid page name";


	}
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['pagename'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) > 0)
	{
		$errors[] = "A page with that name already exists";	
	}

	if (empty($errors))
	{


		$query = "INSERT INTO ".$db->prefix."pages (title,name) VALUES('".$db->escape($fields['pagetitle'])."','c".$db->escape($fields['pagename'])."')";
		$result = $db->query($query);
		return null;
	}
		
	return $errors;

}


?>
