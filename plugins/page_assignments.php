<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");


if ($user->isGuest() != true)
{
	$query = "SELECT * FROM ".$db->prefix."social WHERE name='facebook'";
	$result = $db->query($query);
	if ($db->num_rows($result) > 0)
		$render->assign('fb_enabled',1);

	$query = "SELECT * FROM ".$db->prefix."assignments";
	$result = $db->query($query);
	$assignments = array();
	if ($db->num_rows($result) > 0)
	{
		
		while ($row = $db->fetch_assoc($result))
		{
			if (intval($row['release']) == 0)
				$row['release'] = "Unreleased";
			else
				$row['release'] = "Released";
			$assignments[] = $row;
		}
	}
	$render->assign("assignments",$assignments);


	if (!empty($_POST['submit_add']))
	{

		$errors = doAddAssignment();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_POST['submit_edit']))
	{
		

		$errors = doEditAssignment();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}

	else if (!empty($_GET['edit']))
	{
		$render->assign("edit",true);
		$errors = doPreEditAssignment();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}


	else if (!empty($_GET['remove']))
	{
		$errors = doRemoveAssignment();

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


function doPreEditAssignment()
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

	if (intval($fields['edit']) <= 0 )
	{
		$errors[] = "Invalid assignment id";


	}
	
	if (!empty($errors))

		return $errors;

	$query = "SELECT * FROM ".$db->prefix."assignments WHERE id='".intval($fields['edit'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such assignment exists";	
	}

	if (empty($errors))
	{
		
		$row = $db->fetch_assoc($result);
		$assignmentid = intval($row['id']);
		$assignmentname = $row['name'];
		$assignmentdate = $row['date'];
		if (intval($row['release']) == 1)
			$assignmentrelease = "release";
		else
			$assignmentrelease = "";
		$assignmentcontent = file_get_contents("templates/content/assignment_" . $row['id'] .".htm");
		$render->assign("editassignment_assignmentname",$assignmentname);
		$render->assign("editassignment_assignmentdate",$assignmentdate);
		$render->assign("editassignment_assignmentrelease",$assignmentrelease);
		$render->assign("editassignment_assignmentid",$assignmentid);
		$render->assign("editassignment_assignmentcontent",$assignmentcontent);

		
	}
		
	return $errors;

}


function doEditAssignment()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("assignmentid"=>true,"assignmentdate"=>true,"assignmentname"=>true,"assignmentcontent"=>true,"assignmentrelease"=>false,"assignmentpublish"=>false,"assignmentpublishnotes"=>false));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}


	$render->assign("editassignment_assignmentname",$fields['assignmentname']);

	$render->assign("editassignment_assignmentrelease",$fields['assignmentrelease']);
	$render->assign("editassignment_assignmentdate",$fields['assignmentdate']);
	$render->assign("editassignment_assignmentcontent",$fields['assignmentcontent']);
	$render->assign("editassignment_assignmentpublish",$fields['assignmentpublish']);


	$render->assign("editassignment_assignmentpublishnotes",$fields['assignmentpublishnotes']);

	if (!preg_match("/^[0-1][0-9]\\/[0-3][0-9]\\/20[0-9][0-9]+$/i",$fields['assignmentdate']))
	{
		$errors[] = "Invalid date";


	}


	if (intval($fields['assignmentid']) <= 0 )
	{
		$errors[] = "Invalid assignment id";


	}
	
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."assignments WHERE id='".intval($fields['assignmentid'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such assignment exists";	
	}

	if (empty($errors))
	{

		$orow = $db->fetch_assoc($result);
		$oldrelease = intval($orow['release']);
		if ($fields['assignmentrelease'] == "release")
			$release = 1;
		else
			$release = 0;		

		$row = $db->fetch_assoc($result);
		
		if ($fields['assignmentpublish'] == 'publish' && $release == 0)
		{
			$errors[] = "You cannot publish an unreleased assignment";
			return $errors;
		}

		$nquery = "UPDATE ".$db->prefix."assignments SET `release`='".$release."', `date`='".$db->escape($fields['assignmentdate'])."', name='".$db->escape($fields['assignmentname'])."' WHERE id='".intval($fields['assignmentid'])."'";
		$nresult = $db->query($nquery) or displayError("Error" . $nquery, $db->error());

		file_put_contents("templates/content/assignment_" . intval($fields['assignmentid']) .".htm", $fields['assignmentcontent']);

		if ($fields['assignmentpublish'] == 'publish')
		{
			include(BASE_DIR . "/include/social/facebook.php");
			$squery = "SELECT token FROM ".$db->prefix."social WHERE name='facebook'";
			$sresult = $db->query($squery);
			$srow = $db->fetch_assoc($sresult);
			
			$pinfo = getTokenPage($srow['token']);

			if ($oldrelease != $release)
				makePost($pinfo[0],$pinfo[1],"Assignment released!", $fields['assignmentname'], $config['siteurl']."?page=assignment&id=".intval($fields['assignmentid']), $config['sitename'], $fields['assignmentpublishnotes'], $config['siteurl'] . "/styles/default/images/assignments.png");
			else
				makePost($pinfo[0],$pinfo[1],"Assignment updated!", $fields['assignmentname'], $config['siteurl']."?page=assignment&id=".intval($fields['assignmentid']), $config['sitename'], $fields['assignmentpublishnotes'], $config['siteurl'] . "/styles/default/images/assignments.png");
		}

		redirect("Assignment successfully modified","?page=assignments");
		return null;
		
	}
		
	return $errors;

}



function doRemoveAssignment()
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



	if (intval($fields['remove']) <= 0 )
	{
		$errors[] = "Invalid assignment id";


	}
	
	if (!empty($errors))
		return $errors;

	$query = "SELECT * FROM ".$db->prefix."assignments WHERE id='".intval($fields['remove'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "No such assignment exists";	
	}

	if (empty($errors))
	{
		$row = $db->fetch_assoc($result);
		
		$query = "DELETE FROM ".$db->prefix."assignments WHERE id='".intval($fields['remove'])."'";
		$result = $db->query($query);

		unlink("templates/content/assignment_" . intval($fields['remove']) .".htm");

		redirect("The assignment has been removed", "?page=assignments");

		return null;
	}
		
	return $errors;

}


function doAddAssignment()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("assignmentname"=>true,"assignmentdate"=>true,"assignmentcontent"=>true,"assignmentrelease"=>false,"assignmentpublish"=>false,"assignmentpublishnotes"=>false));


	foreach ($fields as $key => $value)
	{
		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	$render->assign("assignment_assignmentname",$fields['assignmentname']);

	$render->assign("assignment_assignmentrelease",$fields['assignmentrelease']);
	$render->assign("assignment_assignmentdate",$fields['assignmentdate']);
	$render->assign("assignment_assignmentcontent",$fields['assignmentcontent']);
	$render->assign("assignment_assignmentpublish",$fields['assignmentpublish']);
	$render->assign("assignment_assignmentpublishnotes",$fields['assignmentpublishnotes']);

	if ($fields['assignmentrelease'] == "release")
		$release = 1;
	else
		$release = 0;		


	if (!preg_match("/^[0-1][0-9]\\/[0-3][0-9]\\/20[0-9][0-9]+$/i",$fields['assignmentdate']))
	{
		$errors[] = "Invalid date";


	}

	if ($fields['assignmentpublish'] == 'publish' && $release == 0)
	{
		$errors[] = "You cannot publish an unreleased assignment";
	}

	if (empty($errors))
	{


		$query = "INSERT INTO ".$db->prefix."assignments (name,`date`,`release`) VALUES('".$db->escape($fields['assignmentname'])."','".$db->escape($fields['assignmentdate'])."','".$release."')";
		$result = $db->query($query);
		$sid = $db->insert_id();
		file_put_contents("templates/content/assignment_" . $sid .".htm", $fields['assignmentcontent']);


		if ($fields['assignmentpublish'] == 'publish')
		{
			include(BASE_DIR . "/include/social/facebook.php");
			$squery = "SELECT token FROM ".$db->prefix."social WHERE name='facebook'";
			$sresult = $db->query($squery);
			$srow = $db->fetch_assoc($sresult);
			
			$pinfo = getTokenPage($srow['token']);

			
			makePost($pinfo[0],$pinfo[1],"Assignment Released!", $fields['assignmentname'], $config['siteurl']."?page=assignment&id=".intval($sid), $config['sitename'], $fields['assignmentpublishnotes'], $config['siteurl'] . "/styles/default/images/assignments.png");
		}



		redirect("Assignment successfully added","?page=assignments");
		return null;
	}
		
	return $errors;

}


?>
