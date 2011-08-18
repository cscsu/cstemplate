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

require_once(BASE_DIR . "/include/social/zfacebook.php");


$render->assign("permissionurl", generatePermissionURL($config['siteurl']));


if ($_GET['callback'])
{
	echo "callback";
	if ($_GET['code'])
	{
		echo "recieved code";
		$tokenURL = generateTokenURL($config['siteurl'], $_GET['code']);
		echo "\r\n<br>";
		echo "Token URL " . $tokenURL;
		echo "\r\n<br>";
		$session = makeTokenRequest($tokenURL);
		echo "\r\n<br>";
		echo "Session: " .  $session;
		echo "\r\n<br>";
		echo checkToken($session,"151932798221355");
		echo "\r\n<br>";
		echo checkUser($session,"151932798221355");
		echo "\r\n<br>";
		echo makePost($session,"151932798221355","Test Post Message");
		echo "\r\n<br>";

		
	}
}


?>
