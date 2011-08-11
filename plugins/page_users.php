<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");


if (!$user->isGuest())
{


	$query = "SELECT firstname,lastname,email FROM ".$db->prefix."users";
	$result = $db->query($query);
	$siteusers = array();
	if ($db->num_rows($result) > 0)
	{
		
		while ($row = $db->fetch_assoc($result))
		{
			$siteusers[] = $row;
		}
	}
	$render->assign("siteusers",$siteusers);



	if (!empty($_POST['submit']))
	{

		$errors = doGenerateToken();

		if (!empty($errors))

		{

		$render->assign("errors",$errors);

		}
	}
	
}
else
{
	redirect("You must be logged in", "?page=login");
}





function doGenerateToken()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	$fields = validateFields($_POST, array("email"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}

	if (!empty($errors))

		return $errors;

	$query = "SELECT * FROM ".$db->prefix."users WHERE email='".$db->escape($fields['email'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) > 0)
	{
		$errors[] = "A user already exists with that email address";	
	}

	// TODO handle brute force (captcha?)
	if (empty($errors))
	{
		
		$token = hashPass(generateSalt(), generateSalt());

		$query = "INSERT INTO ".$db->prefix."tokens (token) 
			VALUES ('".$db->escape($token)."')";
		$result = $db->query($query);

		$email_to = $fields['email'];
		$email_subject = "Registration For " . $config['sitename'];

		$email_message .= "You may now register for access on the ". $config['sitename'] ." website" . "\r\n\r\n";

	$email_message .= "Please visit the following url to register\r\n";
	$email_message .= "Registration Link: <a href=\"".$config['siteurl']."?page=register&registrationtoken=".$token."\">".$config['siteurl']."?page=register&registrationtoken=".$token."</a>\r\n";


	$email_from = "From: donotreply@cs.colostate.edu\r\n";



	mail($email_to, $email_subject, $email_message, $email_from);
	redirect("An email has been sent to the requested address","?page=users");
	return null;
	}
		
	return $errors;

}


?>
