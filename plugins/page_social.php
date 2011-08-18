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

require_once(BASE_DIR . "/include/social/facebook.php");


$render->assign("permissionurl", generatePermissionURL($config['siteurl']));


if (!empty($_GET['facebook']))
{
	if (!empty($_GET['code']))
	{
		$tokenURL = generateTokenURL($config['siteurl'], $_GET['code']);
		$session = makeTokenRequest($tokenURL);
		$query = "DELETE FROM ".$db->prefix."social WHERE name='facebook'";
		$db->query($query);
		$query = "INSERT INTO ".$db->prefix."social (name,token) VALUES ('facebook','".$db->escape($session)."')";
		$db->query($query);
		$render->assign("facebooksetupcomplete",1);
	}
}
else if (!empty($_POST['submit']))
{
	$errors = array();

	$fields = validateFields($_POST, array("pageurl"=>true));


	foreach ($fields as $key => $value)

	{

		if ($value === -1)

			$errors[] = "Missing " . $key . " field";

	}
	
	if (!empty($errors))
	{

		$render->assign("errors",$errors);
		$render->assign("fbpageurl",$fields['pageurl']);
		$render->assign("facebooksetupcomplete",1);
	}
	else
	{
		$query = "UPDATE ".$db->prefix."social SET url='".$db->escape($fields['pageurl'])."' WHERE name='facebook'";
		$db->query($query);
		redirect("Page information updated","?page=social&complete=true");
	}
}
else
{
	$query = "SELECT url FROM ".$db->prefix."social WHERE name='facebook'";
	$result = $db->query($query);
	if ($db->num_rows($result) > 0)
	{
		$row = $db->fetch_assoc($result);
		$render->assign("fbpageurl",$row['url']);
		$render->assign("facebooksetupcomplete",1);
	}
}
	


?>
