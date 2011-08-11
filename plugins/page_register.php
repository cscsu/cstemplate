<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");


if ($user->isGuest() == true)
{

	if (!empty($_POST['submit']))
	{

		$errors = doRegister();
	
		if (!empty($errors))
	{
		
			$render->assign("errors",$errors);
	
		}
	}
	else
	{
		$fields = validateFields($_GET, array("registrationtoken"=>true));
		foreach ($fields as $key => $value)
	{

			if ($value === -1)

				redirect("Invalid registration Token");
		}
		$render->assign("registertoken",$fields['registrationtoken']);

		
	}
	
}	
else
{
	redirect("You are already logged in as " + $user->getFullName());
}





function doRegister()
{
	global $db;
	global $config;

	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("registrationtoken"=>true,"firstname"=>true,"lastname"=>true, "username"=>true, "password"=>true, "email"=>true));


	foreach ($fields as $key => $value)
	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}
	if (!empty($errors))

		return $errors;

	$query = "SELECT * FROM ".$db->prefix."users WHERE username='".$db->escape($fields['username'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) > 0)
	{
		$errors[] = "Username already exists";	
	}

	$query = "SELECT * FROM ".$db->prefix."tokens WHERE token='".$db->escape($fields['registrationtoken'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		redirect("Invalid registration Token");
		die();
	}

	if (strlen($fields['password']) < 5)
	{
		$errors[] = "Password too short";	
	}

	$salt = generatesalt();
	$hashpass = hashPass($salt,$fields['password']);

	// TODO handle brute force (captcha?)
	if (empty($errors))
	{
	
		$query = "INSERT INTO ".$db->prefix."users (firstname,lastname,username,password,salt,email) 
			VALUES ('".$db->escape($fields['firstname'])."',
				'".$db->escape($fields['lastname'])."',
				'".$db->escape($fields['username'])."',
				'".$db->escape($hashpass)."',
				'".$db->escape($salt)."',
				'".$db->escape($fields['email'])."')";
		$result = $db->query($query);

		$query = "DELETE FROM ".$db->prefix."tokens WHERE token='".$db->escape($fields['registrationtoken'])."'";
		$result = $db->query($query);



		$query = "SELECT * FROM ".$db->prefix."users WHERE username='".$db->escape($fields['username'])."' LIMIT 1";
		$result = $db->query($query);

		if ($db->num_rows($result) > 0)
		{
			$row = $db->fetch_assoc($result);

			// TODO permissions
			$user = new User($row['userid'],$row['username'], $row['firstname'], $row['lastname']);

			$token = hashPass($config['sitekey'],$fields['userid'] . "::" . $_SERVER['REMOTE_ADDR']);

			
			setcookie($config['siteid'], $row['userid'] . "|" . hashPass($row['userid'] . "::" . $_SERVER['REMOTE_ADDR'],$pass) . "|" . $token, 0, '/', "www.cs.colostate.edu", false);
			redirect("You are now registered and logged in " . $user->getFullName());
			return null;
		}
		else
		{
			redirect("Registration Failure");
		}
	}
	else
		$errors[] = "Invalid password";


		
	return $errors;

}


?>
