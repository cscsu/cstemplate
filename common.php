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


// Set the base directory to be the curent directory
DEFINE('BASE_DIR','./');

error_reporting(E_ALL ^ E_NOTICE);


// Load the functions
require_once(BASE_DIR . 'include/functions.php');

// Load the Renderer Class
require_once(BASE_DIR . 'gsRender/gsRender.class.php');

// Load the mysql class
require_once(BASE_DIR . 'include/dblayer/mysql.php');

// Load the user class
require_once(BASE_DIR . 'include/class.user.php');

// Fetch the configuration
$config = fetchConfig();

// Global the config variable
// TODO does this actually do anything?
global $config;

// Initialize the dabase layer
$db = new DBLayer($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname'], $config['dbprefix'], false);

// Global the database layer
// TODO does this actually do anything?
global $db;


// Fetch the local user
$user = getUser();

// Check if this is an extern page request
if (isset($_GET['extern']))
{
	// Verify page name
	if (preg_match("/^[a-zA-Z0-9_]+$/i",$_GET['extern']))
	{
			$extern = $_GET['extern'];
			
			// Verify plugin existance
			if (!file_exists(BASE_DIR . 'plugins/extern_' . $extern . '.php'))
			{
				displayError("Unable to locate requested plugin");
			}
			// Include the page and halt execution
			include BASE_DIR . 'plugins/extern_' . $extern . '.php';
			die();
	}
}

// Default to a none content page
$contentpage = false;

// Get the page
if (isset($_GET['page']))
{
	// Verify valid page name or default to index
	if (preg_match("/^[a-zA-Z0-9_]+$/i",$_GET['page']))
	{
		$page = $_GET['page'];
	}
	else
		$page = "index";	
}
else
{
	$page = "index";
}

// Verify template existance for the page
if (!file_exists("templates/core_" . $page . ".htm"))
{
	// If the page does not exist we will check if its a content page
	// if not default to index
	if (file_exists("templates/content/page_" . $page . ".htm"))
	{
		$contentpage = true;
	}
	else
		$page = "index";
}

// Global the content page variable
// TODO does this do anything?
global $contentpage;

// Global the page variable
// TODO does this do anything?
	global $page;

// Create the menu
$menu_categories = buildMenu();
?>
