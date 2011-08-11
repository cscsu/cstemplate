<?php
//   Copyright 2011
//	Caleb Champlin (champlin@cs.colostate.edu)
//	Jared Koontz (koontz@cs.colostate.edu)
//	Austin Walkup (walkup@cs.colostate.edu)
//	Ross Beveridge (beveridge@cs.colostate.edu)
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.


$render->assign("pageinfo","");
$render->assign("pageid","page1");

// Check if the user is a guest
if ($user->isGuest() == true)
{

	// If the form has been submitted attempt to process the login
	if (!empty($_POST['submit']))
	{
		// Process the login and output and errors
		$errors = doLogin();
		if (!empty($errors))
		{
			$render->assign("errors",$errors);
		}
	}	
}
else
{
	// If they are logged in redirect them to the index
	redirect("You are already logged in as " + $user->getFullName());
}


// Function to process a login
function doLogin()
{

	// Get the global variables
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	// Validate the fields
	$fields = validateFields($_POST, array("username"=>true, "password"=>true));


	foreach ($fields as $key => $value)
	{
		if ($value === -1)
			$errors[] = "Missing " . $key . " field";
	}
	// Return any existing errors
	if (!empty($errors))
		return $errors;

	// Grab the affiliated user from the database
	$query = "SELECT * FROM ".$db->prefix."users WHERE username='".$db->escape($fields['username'])."' LIMIT 1";
	$result = $db->query($query);

	// Insure the user exists
	if ($db->num_rows($result) > 0)
	{
		$row = $db->fetch_assoc($result);

		// Get the hash password
		$pass = hashPass($row['salt'],$fields['password']);

		// TODO handle brute force (captcha?)
		if ($pass == $row['password'])
		{
			// TODO permissions
			// Create a new user instance
			$user = new User($row['userid'],$row['username'], $row['firstname'], $row['lastname']);

			// Generate a cookie token
			// Token is generated using
			// - Sitekey
			// - Part of the user salt
			// - The user id
			// - The ip address
			$token = hashPass($config['sitekey'] . "::" . substr($row['salt'],0,7), $fields['userid'] . "::" . $_SERVER['REMOTE_ADDR']);

			// Set the cookie
			setcookie($config['siteid'], $row['userid'] . "|" . hashPass($row['userid'] . "::" . $_SERVER['REMOTE_ADDR'],$pass) . "|" . $token, 0, '/', "www.cs.colostate.edu", false);
		
			// Redirect them to the index
			redirect("You are now logged in " . $user->getFullName());
			// Return null for no errors
			return null;
		}
		else
			$errors[] = "Invalid password";

	}
	else
	{
		$render->assign("login_username",$fields['username']);
		$errors[] = "Unable to find your username";
	}

		
	return $errors;

}


?>
