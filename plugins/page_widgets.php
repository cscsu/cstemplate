<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");


if ($user->isGuest() != true)
{


	$query = "SELECT * FROM ".$db->prefix."pages WHERE widget=1 AND locked=0";
	$result = $db->query($query);
	$users = array();
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

		$errors = doAddWidget();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_POST['submit_edit']))
	{
		

		$errors = doEditWidget();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_GET['edit']))
	{
		$render->assign("edit",true);
		$errors = doPreEditWidget();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}


	else if (!empty($_GET['remove']))
	{
		$errors = doRemoveWidget();

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


function doPreEditWidget()
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
		$errors[] = "Invalid widget name";


	}
	
	if (!empty($errors))

		return $errors;

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['edit'])."' AND widget=1 AND locked=0 LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such widget exists";	
	}

	if (empty($errors))
	{
		
		$row = $db->fetch_assoc($result);
		$widgetname = $row['name'];
		$widgetcontent = file_get_contents("templates/content/widget_" . $fields['edit'] .".htm");
		$render->assign("editwidget_widgetname",$widgetname);



		$render->assign("editwidget_widgetcontent",$widgetcontent);

		
	}
		
	return $errors;

}


function doEditWidget()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("widgetname"=>true,"widgetcontent"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	$render->assign("editwidget_widgetname",$fields['widgetname']);


	$render->assign("editwidget_widgetcontent",$fields['widgetcontent']);


	if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['widgetname']))
	{
		$errors[] = "Invalid widget name";


	}
	
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['widgetname'])."' AND widget=1 AND locked=0 LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such widget exists";	
	}

	if (empty($errors))
	{
		

		$row = $db->fetch_assoc($result);
		

		file_put_contents("templates/content/widget_" . $fields['widgetname'] .".htm", $fields['widgetcontent']);
		redirect("Widget successfully modified","?page=widgets");
		return null;
		
	}
		
	return $errors;

}



function doRemoveWidget()
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
		$errors[] = "Invalid widget name";


	}
	
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['remove'])."' AND widget=1 AND locked=0 LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such widget exists";	
	}

	if (empty($errors))
	{
		$row = $db->fetch_assoc($result);
		
		$query = "DELETE FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['remove'])."' AND Widget=1";
		$result = $db->query($query);

		unlink("templates/content/widget_" . $fields['remove'] .".htm");

		redirect("The widget has been removed", "?page=widgets");

		return null;
	}
		
	return $errors;

}


function doAddWidget()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("widgetname"=>true,"widgetcontent"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}



	if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['widgetname']))
	{
		$errors[] = "Invalid widget name";


	}
	if (!empty($errors))
		return $errors;


	$fields['widgetname'] = 'c' . strtolower($fields['widgetname']);

	$query = "SELECT * FROM ".$db->prefix."pages WHERE name='".$db->escape($fields['widgetname'])."' AND widget=1 LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) > 0)
	{
		$errors[] = "A widget with that name already exists";	
	}

	if (empty($errors))
	{


		$query = "INSERT INTO ".$db->prefix."pages (name, widget) VALUES('".$db->escape($fields['widgetname'],'1')."')";
		$result = $db->query($query);
		file_put_contents("templates/content/widget_" . $fields['widgetname'] .".htm", $fields['widgetcontent']);
		redirect("Widget successfully added","?page=widgets");
		return null;
	}
		
	return $errors;

}


?>
