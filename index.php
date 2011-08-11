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


// Include the common file
require_once("common.php");

// Initialize the web template engine
$render = new gsRender(false);


// Verify whether or not this is a core page or a content page
if (!$contentpage)
{
	// In the event its a core page insure that a plugin exists for the page
	if (!file_exists(BASE_DIR . 'plugins/page_' . $page . '.php'))
	{
		displayError("Unable to locate requested plugin");
	}
}

// Assign some core variables to the template engine
$render->assign("menu_categories", $menu_categories);
$render->assign("page",$page);

// Assign the user variable o the template engine.
$render->assign("user", $user);

// If its not a content page include the plugin code
if (!$contentpage)
{
	include BASE_DIR . 'plugins/page_' . $page . '.php';
}


// Fetch the header and output it
echo $render->fetch('header.htm');

// Determine the appropriate page to fetch depending on content or core
if (!$contentpage)
{
	echo $render->fetch('page_' . $page . '.htm');
}
else
{
	echo $render->fetch('content/page_' . $page . '.htm');
}

// Fetch the footer and output it
echo $render->fetch('footer.htm');
?>
