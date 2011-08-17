<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");


if ($user->isGuest() != true)
{


	$query = "SELECT * FROM ".$db->prefix."pages WHERE widget=0";
	$result = $db->query($query);
	$pages = array();
	if ($db->num_rows($result) > 0)
	{
		
		while ($row = $db->fetch_assoc($result))
		{
			$pages[] = $row;
		}
	}
	$render->assign("pages",$pages);


	$query = "SELECT * FROM ".$db->prefix."pages WHERE widget=1";
	$result = $db->query($query);
	$widgets = array();
	if ($db->num_rows($result) > 0)
	{
		
		while ($row = $db->fetch_assoc($result))
		{
			$widgets[] = $row;
		}
	}
	$render->assign("widgets",$widgets);


	if (!empty($_POST['submit_add']))
	{

		$errors = doAddPage();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_POST['submit_edit']))
	{
		

		$errors = doEditPage();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_GET['edit']))
	{
		$render->assign("edit",true);
		$errors = doPreEditPage();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}


	else if (!empty($_GET['remove']))
	{
		$errors = doRemovePage();

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

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['edit'])."' AND widget=0 LIMIT 1";
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

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['pagename'])."' AND widget=0 LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such page exists";	
	}

	if (empty($errors))
	{
		

		$row = $db->fetch_assoc($result);
		

		$query = "UPDATE ".$db->prefix."pages SET title='".$db->escape($fields['pagetitle'])."' WHERE widget=0 AND name='".$db->escape($fields['pagename'])."'";
		$result = $db->query($query);


		file_put_contents("templates/content/page_" . $fields['pagename'] .".htm", $fields['pagecontent']);
		redirect("Page successfully modified","?page=pages");
		return null;
		
	}
		
	return $errors;

}



function doRemovePage()
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



	if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['remove']))
	{
		$errors[] = "Invalid page name";


	}
	
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['remove'])."' AND widget=0 LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such page exists";	
	}

	if (empty($errors))
	{
		$row = $db->fetch_assoc($result);
		
		$query = "DELETE FROM ".$db->prefix."menu WHERE pageid='".intval($row['id'])."'";
		$result = $db->query($query);

		$query = "DELETE FROM ".$db->prefix."pages WHERE widget=0 AND name='".$db->escape($fields['remove'])."'";
		$result = $db->query($query);

		unlink("templates/content/page_" . $fields['remove'] .".htm");

		redirect("The page has been removed", "?page=pages");

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


	$fields['pagename'] = 'c' . strtolower($fields['pagename']);

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['pagename'])."' AND widget=0 LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) > 0)
	{
		$errors[] = "A page with that name already exists";	
	}

	if (empty($errors))
	{


		$query = "INSERT INTO ".$db->prefix."pages (title,name) VALUES('".$db->escape($fields['pagetitle'])."','".$db->escape($fields['pagename'])."')";
		$result = $db->query($query);
		file_put_contents("templates/content/page_" . $fields['pagename'] .".htm", $fields['pagecontent']);
		redirect("Page successfully added","?page=pages");
		return null;
	}
		
	return $errors;

}


?>
