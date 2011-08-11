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

// Generate the menu for the top navigation bar
$menu = array();

// Check whether or not the user is a guest	
	
if ($user->isGuest())
{
	// If the user is a guest get the appropriate menu items
	$query = "SELECT m.position as position, m.id as id, p.title as title, p.name as name, m.direct as direct, m.url as durl, m.title as dtitle, m.requireslogin as rlogin FROM ".$db->prefix."menu as m LEFT JOIN ".$db->prefix."pages as p ON (m.pageid = p.id) WHERE requireslogin=0 ORDER BY m.position ASC";
}

else
	
{
	// Get the non guest menu items as well

	$query = "SELECT m.position as position, m.id as id, p.title as title, p.name as name, m.direct as direct, m.url as durl, m.title as dtitle, m.requireslogin as rlogin FROM ".$db->prefix."menu as m LEFT JOIN ".$db->prefix."pages as p ON (m.pageid = p.id) ORDER BY m.position ASC";

}	

// Get the meny items from the query	
$result = $db->query($query);
if ($db->num_rows($result) > 0)
{
	while ($row = $db->fetch_assoc($result))
	{
			
		// Identify the first item
		if ($row['position'] == 1)
				
			$first = true;
			
		else
				
			$first = false;

		
		// Identify the last item
		if ($row['position'] == $db->num_rows($result))
				
			$last = true;
			
		else
	
			$last = false;
				
			

		// Generate the item based on whether or not its a direct link or an internal link
		if (intval($row['direct']) == 0)
		{
			if ($row['name'] == $page)
				$menu[] = array("caption"=>$row['title'],"page"=>$row['name'],"first"=>$first,"last"=>$last);
			else
				$menu[] = array("caption"=>$row['title'],"current"=>true,"page"=>$row['name'],"first"=>$first,"last"=>$last);
		}
		else
			$menu[] = array("caption"=>$row['dtitle'],"url"=>$row['durl'],"first"=>$first,"last"=>$last);
	}
}
	

$menu_categories[0] = array("caption"=>"Menu","menu"=>$menu);
?>
