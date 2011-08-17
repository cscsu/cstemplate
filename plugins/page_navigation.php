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

// Verify the user is not a guest
if ($user->isGuest() != true)
{


	// Get all the existing menu items
	$query = "SELECT m.position as position, m.id as id, p.title as title, p.name as name, m.direct as direct, m.url as durl, m.title as dtitle, m.requireslogin as rlogin FROM ".$db->prefix."menu as m LEFT JOIN ".$db->prefix."pages as p ON (m.pageid = p.id) ORDER BY m.position ASC";
	$result = $db->query($query);
	$links = array();
	if ($db->num_rows($result) > 0)
	{
		
		while ($row = $db->fetch_assoc($result))
		{
			
			if (intval($row['direct']) == 1)
			
			{
				
				$row['name'] = $row['dtitle'];
				
				$row['title'] = $row['dtitle'];

			}
			
			$links[] = $row;
		}
	}
	$render->assign("menuitems",$links);

	// Get all the available menu items
	$query = "SELECT p.title as title, p.name as name FROM ".$db->prefix."pages as p LEFT JOIN ".$db->prefix."menu as m ON (m.pageid = p.id) WHERE p.widget=0 AND  m.pageid is NULL";
	$result = $db->query($query);
	$pages = array();
	if ($db->num_rows($result) > 0)
	{
		
		while ($row = $db->fetch_assoc($result))
		{
			$pages[] = $row;
		}
	}
	$render->assign("availablepages",$pages);

	// Creating a direct link and processing the request
	if (!empty($_POST['submit_dlink']))
	{

		$errors = doAddDirectLink();

		if (!empty($errors))

		{

			$render->assign("derrors",$errors);

		}
	}
	// Remove an existing link
	
	else if (!empty($_GET['remove']))
	{

		$errors = doRemoveLink(intval($_GET['remove']),$links);

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}
	// Edit a link	
	else if (!empty($_GET['edit']))
	{

		$errors = doPreEditLink(intval($_GET['edit']));

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}
	// Edit a link	
	else if (!empty($_POST['submit_edit']))
	{

		$errors = doEditLink();

		if (!empty($errors))

		{

			$render->assign("errors",$errors);

		}
	}
	// Creating a page link
	else if (!empty($_POST['submit_plink']))
	{

		$errors = doAddPageLink();

		if (!empty($errors))

		{
			$render->assign("perrors",$errors);
		
		}
	}
	// Move up a link
	else if (!empty($_GET['up']))
	{
		$errors = doUpPage(intval($_GET['up']),$links);

		if (!empty($errors))
		
		{
			$render->assign("errors",$errors);
		
		}
	}

	// Move down a link	
	else if (!empty($_GET['down']))
	{
		$errors = doDownPage(intval($_GET['down']),$links);

		if (!empty($errors))

		{
			$render->assign("errors",$errors);
		
		}
	}
	
}
else
{

	// Redirect to index if the user is not logged in.
	redirect("You must be logged in");
}



// Function to add a page link
function doAddPageLink()
{
	global $db;
	global $config;
	
	global $render;
	global $user;
	$errors = array();

	// Validate form fields
	$fields = validateFields($_POST, array("pagename"=>true,"pageposition"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}


	// Insure a valid page name
	if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['pagename']))
	{
		$errors[] = "Invalid page name";

	}
	

	// Insure a valid page position
	if (intval($fields['pageposition']) < 0)
		
		$errors[] = "Invalid page position";


	// Check for errors and return if there are any		
	if (!empty($errors))
		
		return $errors;

	// Insure that the page exists in the database
	$query = "SELECT * FROM ".$db->prefix."pages WHERE widget=0 AND name='".$db->escape($fields['pagename'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "A page with that name does not exist";	
	}

	// Verify no errors
	if (empty($errors))
	{
		
		
		$row = $db->fetch_assoc($result);
		

		// Reorganize the existing items		
		$query = "UPDATE ".$db->prefix."menu SET position=position+1 WHERE position>=".(intval($fields['pageposition'])+1)."";		
		
		$result = $db->query($query);
		
		// Insert the item into the database
		$query = "INSERT INTO ".$db->prefix."menu (pageid, position) VALUES('".intval($row['id'])."','".intval($fields['pageposition']+1)."')"; 
		$result = $db->query($query);


		// Redirect to the navigation page
		redirect("Menu item added", "?page=navigation");
	}
		
	return $errors;

}
// Add a direct link
function doAddDirectLink()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();
	// Validate fields
	$fields = validateFields($_POST, array("linktitle"=>true,"linkurl"=>true,"pageposition"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}


	// Insure a valid page position
	if (intval($fields['pageposition']) < 0)
		
		$errors[] = "Invalid page position";

		
	// Check for erros and return
	if (!empty($errors))

		return $errors;

	// Reorder the existin pages	
		
	$query = "UPDATE ".$db->prefix."menu SET position=position+1 WHERE position>=".(intval($fields['pageposition'])+1)."";		
		
	$result = $db->query($query);
	// Insert the link
		
	$query = "INSERT INTO ".$db->prefix."menu (pageid, position, direct, title, url) VALUES('0','".intval($fields['pageposition']+1)."','1','".$db->escape($fields['linktitle'])."','".$db->escape($fields['linkurl'])."')";
	$result = $db->query($query);

		
	redirect("Menu item added", "?page=navigation");

		
	return $errors;

}


// Remove a page
function doPreEditLink($id)
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	if ($id <= 0)
			$errors[] = "Invalid link id";

	$query = "SELECT * FROM ".$db->prefix."menu WHERE id='".intval($id)."' LIMIT 1";
	// Insure that the page exists in the database
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "A link with that id does not exist";	
	}
	else
	{
		$row = $db->fetch_assoc($result);
		if (intval($row['direct']) == 1)
		{
			$render->assign("editdlink",1);
		}
		else
		{


			// Get all the available menu items
			$query = "SELECT p.title as title, p.name as name FROM ".$db->prefix."pages as p LEFT JOIN ".$db->prefix."menu as m ON (m.pageid = p.id) WHERE p.widget=0 AND (m.pageid is NULL OR m.pageid='".intval($id)."')";
			$result = $db->query($query);
			$pages = array();
			if ($db->num_rows($result) > 0)
			{
			
				while ($row = $db->fetch_assoc($result))
				{
					$pages[] = $row;
				}
			}
			$render->assign("editpages",$pages);

			$render->assign("editplink",1);
			$query = "SELECT p.name as name, m.position as position, m.id as id FROM ".$db->prefix."menu as m LEFT JOIN ".$db->prefix."pages as p ON (p.id = m.pageid)  WHERE p.widget=0 AND m.id='".intval($id)."' LIMIT 1";
			$result = $db->query($query);
			$row = $db->fetch_assoc($result);
		}

		$render->assign("editlink",$row);
		return null;
	}
	return $errors;
}



// Add a direct link
function doEditLink()
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();
	// Validate fields
	$fields = validateFields($_POST, array("linkid"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}



	// Check for errors and return if there are any		
	if (!empty($errors))
		
		return $errors;

	// Insure that the page exists in the database
	$query = "SELECT * FROM ".$db->prefix."menu WHERE id='".intval($fields['linkid'])."' LIMIT 1";
	$result = $db->query($query);

	if ($db->num_rows($result) <= 0)
	{
		$errors[] = "Invalid link id";	
	}
	$id = intval($fields['linkid']);

		
	// Check for erros and return
	if (!empty($errors))

		return $errors;

	$row = $db->fetch_assoc($result);

	if (intval($row['direct']) == 1)
	{
		// Validate fields
		$fields = validateFields($_POST, array("linktitle"=>true,"linkurl"=>true));


		foreach ($fields as $key => $value)

		{

			if ($value === -1)

				$errors[] = "Missing " . $key . " field";

		}



		// Check for erros and return
		if (!empty($errors))

		return $errors;
	
		
		$query = "UPDATE ".$db->prefix."menu SET title='".$db->escape($fields['linktitle'])."',url='".$db->escape($fields['linkurl'])."' WHERE id='".intval($id)."'";		
		
		$result = $db->query($query);
	}
	else
	{


		// Validate fields
		$fields = validateFields($_POST, array("pagename"=>true));


		foreach ($fields as $key => $value)

		{

			if ($value === -1)

				$errors[] = "Missing " . $key . " field";

		}



		// Insure a valid page name
		if (!preg_match("/^[a-zA-Z0-9_]+$/i",$fields['pagename']))
		{
			$errors[] = "Invalid page name";

		}
	

		// Check for errors and return if there are any		
		if (!empty($errors))
		
			return $errors;

		// Insure that the page exists in the database
		$query = "SELECT * FROM ".$db->prefix."pages WHERE widget=0 AND name='".$db->escape($fields['pagename'])."' LIMIT 1";
		$result = $db->query($query);

		if ($db->num_rows($result) <= 0)
		{
			$errors[] = "A page with that name does not exist";	
		}


		// Check for erros and return
		if (!empty($errors))

			return $errors;
	
		
		$query = "UPDATE ".$db->prefix."menu SET pagename='".$db->escape($fields['pagename'])."' WHERE id='".intval($id)."'";		
		
		$result = $db->query($query);

	}

	
	
		
	redirect("Menu item updated", "?page=navigation");

		
	return $errors;

}



// Remove a link
function doRemoveLink($id, $links)
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	if ($id <= 0)
			$errors[] = "Invalid link id";

	$found = false;
	
	// Locate the position for the link
	foreach ($links as $link)
	
	{
		
		if (intval($link['id']) == $id)
		
		{
			
			$found = true;
			
			$position = $link['position'];

			break;
		
		}
	
	}
	
	
	// If the position is not found we have an invalid link
	if ($found == false)

	{
		$errors[] = "Invalid link id";

	}
	



	if (!empty($errors))

		return $errors;



	// Update the link to the new position
	$query = "DELETE FROM ".$db->prefix."menu WHERE id=".$id."";
	$result = $db->query($query);

	// Reorder the other links
	$query = "UPDATE ".$db->prefix."menu SET position=position-1 WHERE position>=".(intval($position))."";
	$result = $db->query($query);


	redirect("The link has been removed","?page=navigation");

}



// Move a page up
function doUpPage($id, $links)
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	if ($id <= 0)
			$errors[] = "Invalid link id";

	$found = false;
	
	// Locate the position for the link
	foreach ($links as $link)
	
	{
		
		if (intval($link['id']) == $id)
		
		{
			
			$found = true;
			
			$position = $link['position'];

			break;
		
		}
	
	}
	
	
	// If the position is not found we have an invalid link
	if ($found == false)

	{
		$errors[] = "Invalid link id";

	}
	else if ($position <= 1)

	{

		$errors[] = "Cannot move up the first link";

	}	



	if (!empty($errors))

		return $errors;

	// Reorder the other links
	$query = "UPDATE ".$db->prefix."menu SET position=position+1 WHERE position=".(intval($position)-1)."";
	$result = $db->query($query);
	// Update the link to the new position
	$query = "UPDATE ".$db->prefix."menu SET position=position-1 WHERE id=".$id."";
	$result = $db->query($query);

	redirect("The link has been moved up","?page=navigation");

}


function doDownPage($id, $links)
{
	global $db;
	global $config;
	global $render;
	global $user;
	$errors = array();

	if ($id <= 0)
			$errors[] = "Invalid link id";

	$found = false;
	
	foreach ($links as $link)

	{

		if (intval($link['id']) == $id)

		{

			$found = true;

			$position = $link['position'];

			break;

		}

	}

	if ($found == false)

		$errors[] = "Invalid link id";

	else if ($position >= count($links))

	{

		$errors[] = "Cannot move down the last link";
	}	


	if (!empty($errors))

		return $errors;


	$query = "UPDATE ".$db->prefix."menu SET position=position-1 WHERE position=".(intval($position)+1)."";
	$result = $db->query($query);
	$query = "UPDATE ".$db->prefix."menu SET position=position+1 WHERE id=".$id."";
	$result = $db->query($query);

	redirect("The link has been moved down","?page=navigation");

}



?>
