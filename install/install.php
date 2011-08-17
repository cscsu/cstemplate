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

// Set base directory one directory up
DEFINE('BASE_DIR','../');

// Declare minimum version
// TODO change this to php5?
define('REQ_PHP_VERSION', '4.3.0');



// Load the functions
require_once(BASE_DIR . 'include/functions.php');

// Load the Renderer Class
require_once(BASE_DIR . 'gsRender/gsRender.class.php');

// Verify valid version
if (!function_exists('version_compare') || version_compare(PHP_VERSION, REQ_PHP_VERSION, '<'))
	displayError('Your PHP version is not valid for this software, please update PHP to version ' . REQ_PHP_VERSION . ' to continue the installation');

// Report everything but notices
error_reporting(E_ALL ^ E_NOTICE);

// Create a generic configuration array so templates work
$config = array();
$config['sitename'] = 'Installation';
$config['style'] = 'default';

if (isset($_POST['createdb']))
{
	
	createDB();

}
else if ($_POST['setupdb'])
{

	setupDB();

}
else if ($_POST['writeconfig'])
{
	writeConfig();
}
else
{
	// Load the getting started page
	$config['subsite'] = 'Getting Started';
	$render = new gsRender(false);
	$render->assign('style', 'default');
	$render->assign('config',$config);
	$render->assign('pagedir','../');
	echo $render->fetch('header.htm');
	echo $render->fetch('install.htm');
	echo $render->fetch('footer.htm');
	exit();
}

function setupDB()
{
	// Load the database setup page		
	$config['subsite'] = 'Database Setup';
	$render = new gsRender(false);
	$render->assign('style', 'default');
	$render->assign('config',$config);
	$render->assign('pagedir','../');
	echo $render->fetch('header.htm');
	echo $render->fetch('install_database.htm');
	echo $render->fetch('footer.htm');
		exit();
}

function writeConfig()
{
	$config['subsite'] = 'Site Configuration';
	// Get our post variables
	$sitename = $_POST['sitename'];
	$subsite = $_POST['subsite'];
	$siteid = $_POST['siteid'];
	// Generate a salt for the site key
	$sitekey = generatesalt();
	$siteurl = $_POST['siteurl'];

	// Get the passed along database information
	$db_host = trim($_POST['db_host']);
	$db_name = trim($_POST['db_name']);
	$db_username = trim($_POST['db_username']);
	$db_password = trim($_POST['db_password']);
	$db_prefix = trim($_POST['db_prefix']);
	

	// Validate form
	if (strlen($sitename) == 0)
		$errors[] = 'Missing site name';
	if (strlen($subsite) == 0)
		$errors[] = 'Missing site sub title';
	if (strlen($siteid) == 0)
		$errors[] = 'Missing site id';
	if (strlen($siteurl) == 0)
		$errors[] = 'Missing site url';


	$errors = array();

	// Insure that we can write to the config file
	if (!is_writable(BASE_DIR . '/config'))
		$errors[] = 'Cannot write to config please ensure it is chmodded to 711';
	// Check for errors thus far
	if (empty($errors))
	{
		// Open the config file for writing
		$fid = fopen(BASE_DIR . '/config/config.php','w');

		// Generate the config ouput
		$output = "<?php \n";
		$output .= "\$config['sitename'] = '" . $sitename . "';\n";
		$output .= "\$config['subsite'] = '" . $subsite . "';\n";
		$output .= "\$config['siteid'] = '" . $siteid . "';\n";
		$output .= "\$config['sitekey'] = '" . $sitekey . "';\n";
		$output .= "\$config['siteurl'] = '" . $siteurl . "';\n";
		$output .= "\$config['dbuser'] = '" . $db_name . "';\n";
		$output .= "\$config['dbpass'] = '" . $db_password . "';\n";
		$output .= "\$config['dbname'] = '" . $db_name . "';\n";
		$output .= "\$config['dbprefix'] = '" . $db_prefix . "';\n";
		$output .= "\$config['dbhost'] = '" . $db_host . "';\n";
		$output .= "\$config['style'] = 'default';\n";
		$output .= "?>";
	
		// Attempt to write the config output
		$write = fwrite($fid, $output);
		
		fclose($fid);
		if ($write !== FALSE)
		{
			// Write succeeded move to the final step
			$config['subsite'] = 'Install Complete';
			$render = new gsRender(false);
			$render->assign('style', 'default');
			$render->assign('config',$config);
			$render->assign('pagedir','../');
			echo $render->fetch('header.htm');
			echo $render->fetch('install_complete.htm');
			echo $render->fetch('footer.htm');
			exit();
		}
		else
			$errors['Failed to write config, please ensure it is properly chmodded'];
	}
		
	// We had an error somewhere reload the config setup page
	$render = new gsRender(false);
	$render->assign('style', 'default');
	$render->assign('errors', $errors);
	$render->assign('sitename', $sitename);
	$render->assign('subsite', $subsite);
	$render->assign('siteid', $siteid);
	$render->assign('siteurl', $siteurl);
	$render->assign('pagedir','../');
	$render->assign('db_host', $db_host);
	$render->assign('db_username', $db_username);
	$render->assign('db_name', $db_name);
	$render->assign('db_password', $db_password);
	$render->assign('db_prefix', $db_prefix);
	$render->assign('config',$config);


	echo $render->fetch('header.htm');
	echo $render->fetch('install_config.htm');
	echo $render->fetch('footer.htm');
	exit();
}

function createDB()
{
	$errors = array();

	// Grab all of out post variables
	$db_host = trim($_POST['db_host']);
	$db_name = trim($_POST['db_name']);
	$db_username = trim($_POST['db_username']);
	$db_password = trim($_POST['db_password']);
	$db_prefix = trim($_POST['db_prefix']);
	$username = trim($_POST['username']);
	$password1 = trim($_POST['password1']);
	$password2 = trim($_POST['password2']);
	$firstname = trim($_POST['firstname']);
	$lastname = trim($_POST['lastname']);
	$email = trim($_POST['email']);

	// Validate form
	if (strlen($db_name) == 0)
		$errors[] = 'Missing db name';
	if (strlen($db_host) == 0)
		$errors[] = 'Missing db host';
	if (strlen($db_username) == 0)
		$errors[] = 'Missing db user';
	if (strlen($db_password) == 0)
		$errors[] = 'Missing db password';
	if (strlen($db_password) == 0)
		$errors[] = 'Missing db password';
	if (strlen($username) < 2)
		$errors[] = 'Username too short or missing';
	if (strlen($username) > 25)
		$errors[] = 'Username too long';
	if (strlen($password1) < 4)
		$errors[] = 'Password too short or missing';
	if (strlen($firstname) == 0)
		$errors[] = 'Missing first name';
	if (strlen($lastname) == 0)
		$errors[] = 'Missing last name';
	if (strlen($email) == 0)
		$errors[] = 'Missing email';
	if ($password1 != $password2)
		$errors[] = 'Passwords do not match';
	if (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $username) || preg_match('/((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))/', $username))
		$errors[] = 'Username is IP';
	if ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
		$errors[] = 'Invalid username';

	// Include the database layer
	require BASE_DIR . 'include/dblayer/mysql.php';


	// Validate prefix
	if (strlen($db_prefix) > 0 && (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $db_prefix) || strlen($db_prefix) > 40))
		$errors[] = 'Invalid database prefix';


	// Check for errors thus far
	if (empty($errors))
	{
		// Initialize the database layer
		$db = new DBLayer($db_host, $db_username, $db_password, $db_name, $db_prefix, false);

		// Verify an installation has not already occured
		$query = "SELECT 1 FROM ".$db->prefix."users WHERE id = 1";

		$result = $db->query($query);
		if ($db->num_rows($result))
			displayError('Already installed');

	}
	if (empty($errors))
	{

		// Generate a salt for the first user
		$salt = generatesalt();
		// Create a hashed password
		$hashpass = hashPass($salt,$password1);
		
		// Setup variables and matches for the schema templates
		// TODO move this to an external function?
		$vars = array();
		$patterns = array();
		$vars['dbprefix'] = $db->escape($db_prefix);
		$vars['username'] = $db->escape($username);
		$vars['password'] = $hashpass;
		$vars['salt'] = $salt;
		$vars['firstname'] = $db->escape($firstname);
		$vars['lastname'] = $db->escape($lastname);
		$vars['email'] = $db->escape($email);
		foreach ($vars as $key => $value)
		{
			$patterns[] = '/<!--\['. $key . '\]-->/i';
		}
		// Install the tables from the schema
		$idir = dir(BASE_DIR . 'install/schema/tables');
		while (($file = $idir->read()) !== false) 
		{
   			if ($file != '.' && $file != '..' && $file != 'index.html' && $file != '/index.html' && !is_dir($idir . '/' . $file))
			{

				// Open the file
				$fid = fopen(BASE_DIR . 'install/schema/tables/' . $file, 'r');
				// Read its contents into a query variable
				$query = fread($fid, filesize(BASE_DIR . 'install/schema/tables/' . $file));
				
				fclose($fid);

				// Replace template variables out of the query
				$query = preg_replace($patterns,$vars,$query);

				// Execute the query
				$db->query($query) or displayError("Table Query Failed: ", $db->error());
			}
		}
		$idir->close();

		// Run the insert queries from the schema
		$idir = dir(BASE_DIR . 'install/schema/inserts');
		while (($file = $idir->read()) !== false) {
			if ($file != '.' && $file != '..' && $file != 'index.html' && $file != '/index.html' && !is_dir($idir . '/' . $file))
			{
				// Open the file	
				$fid = fopen(BASE_DIR . 'install/schema/inserts/' . $file, 'r');
				
				// Read it's contents into a query variable
				$query = fread($fid, filesize(BASE_DIR . 'install/schema/inserts/' . $file));
				
				fclose($fid);

				// Replace the template variables out of the query
				$query = preg_replace($patterns,$vars,$query);

				// Execute the query
				$db->query($query) or displayError("Insert Query Failed: ", $db->error());
			}
		}
		$idir->close();

		// Database installation is complete, move to the next phase

		$render = new gsRender(false);
		$config['subsite'] = 'Configuration Setup';
		$render->assign('pagedir','../');
		$render->assign('style', 'default');
		$render->assign('config',$config);
		$render->assign('db_host', $db_host);
		$render->assign('db_username', $db_username);
		$render->assign('db_name', $db_name);
		$render->assign('db_password', $db_password);
		$render->assign('db_prefix', $db_prefix);

		// Configuration installation
		echo $render->fetch('header.htm');
		echo $render->fetch('install_config.htm');
		echo $render->fetch('footer.htm');
		exit();

	}
	else
	{

		// We've experienced an error
		// reload the database setup page
		$render = new gsRender(false);
		$config['subsite'] = 'Database Setup';
		$render->assign('errors', $errors);
		$render->assign('config',$config);
		$render->assign('username', $username);
		$render->assign('firstname', $firstname);
		$render->assign('lastname', $lastname);
		$render->assign('email', $email);
		$render->assign('db_host', $db_host);
		$render->assign('db_username', $db_username);
		$render->assign('db_name', $db_name);
		$render->assign('db_password', $db_password);
		$render->assign('db_prefix', $db_prefix);
		$render->assign('pagedir','../');
		echo $render->fetch('header.htm');
		echo $render->fetch('install_database.htm');
		echo $render->fetch('footer.htm');
		exit();
	}

}
?>
