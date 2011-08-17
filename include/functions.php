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

// Fetch the configuration file
function fetchConfig()
{
	$errors = array();
	// If the configuration file does not exist
	// primpt the user to install
	if (!file_exists(BASE_DIR . 'config/config.php'))
	{
		displayError('Unable to find configuration file. Please install the software first. <a href="install/install.php">Click here to begin installation</a>');
		die();
	}
	else if (file_exists(BASE_DIR . 'install/install.php'))
	{
		displayError('Please delete the installation script under install/install.php');
		die();
	}
	include BASE_DIR . 'config/config.php';
	return $config;
	
}

// Language support is not active at the moment
function loadScriptLanguageFiles($lang, $scriptfile=true)
{
	$lang = (!empty($lang)) ? preg_replace('#[\.\\\/]#', '', $lang) : 'English';

	if (!file_exists(BASE_DIR.'lang/'.$lang.'/common.php'))
	displayError('Invalid language:' . $lang . ', please verify the language you selected is valid and try again');

	$language = new lang;

	if ($scriptfile)
	{
		$script_file = scriptRequestFile();
		if (!file_exists(BASE_DIR.'lang/'.$lang.'/'.$script_file))
			displayError('Invalid language, please verify the language you selected is valid and try again');
		
	
		require BASE_DIR.'lang/'.$lang.'/'.$script_file;
	}
	require BASE_DIR.'lang/'.$lang.'/common.php';
	


	return $language;

}
// Used to validate fields in form data
function validateFields($refArray,$wanted)
{
	$fields = array();
	// Loop through each wanted field
	foreach ($wanted as $key => $value)
	{
		// Check if the input array contains an index with the same name
		// and ensure that it's value is not empty
		if (trim($refArray[$key]) != "")
		{
			$fields[$key] = $refArray[$key];
		}
		else 
		{
			// If the field is required and not included we set the return value for this field to be negative one
			if ($value == true)
			{
				$fields[$key] = -1;
			}
		}
	}	
	return $fields;
}
// Password hashing mechanism
// Returns: sha1 hash of the password and salt
function hashPass($salt,$password)
{
	// We salt all our passwords
	return sha1($salt . "::" . $password);
}
// Salt generation method
// Returns 15 character salt
function generateSalt()
{
	$salt = "";
	for ($x = 0; $x < 15; $x++)
	{
		$salt .= chr(rand(48,122));
	}
	return $salt;
}
// Grabs the current user
// Returns a guest user if the user is invalid or a valid user instance
function getUser()
{
	// We need the config and database variable
	global $config;
	global $db;
	// Check if a cookie exists with the site ID
	if (!empty($_COOKIE[$config['siteid']]))
	{
		// Split the cookie into its components
		if (preg_match('/(?P<userid>\d+)\|(?P<pword>[0-9a-fA-F]+)\|(?P<token>[0-9a-fA-F]+)/', $_COOKIE[$config['siteid']], $matches))
		{	
			$fields = array("userid"=>$matches["userid"],"pword"=>$matches["pword"],"token"=>$matches["token"]);
		}
		else 
		{
			// If the cookie was improperly formatted return a guest user
			return new User();
		}


		$query = "SELECT * FROM ".$db->prefix."users WHERE userid='".intval($fields['userid'])."' LIMIT 1";
		$result = $db->query($query);

		if ($db->num_rows($result) > 0)
		{
			$row = $db->fetch_assoc($result);



			// Prevent hijacking sessions
			// Each cookie is generated using the site key (random salt), part of the users salt, the userid, and 
			//the current users IP address
			// This mechanism helps to prevent malicious users from stealing someone's cookie and gaining access
			// to the site
			$token = hashPass($config['sitekey'] . "::" . substr($row['salt'],0,7), $fields['userid'] . "::" . $_SERVER['REMOTE_ADDR']);

			// Compare the two tokens
			if ($fields['token'] != $token)
			{
				return new User();
			}
			
			$pass = $fields['pword'];

			// The password in the cookie is a hash of the userid, the users ip address and their password hash
			if ($pass == hashPass($fields['userid'] . "::" . $_SERVER['REMOTE_ADDR'], $row['password']))
			{
				// Everything seems valid generate the user
				// TODO permissions
				return new User($row['userid'],$row['username'], $row['firstname'], $row['lastname']);
			
			}
			else
			{
				// Password mismatch return a guest user	
				return new User();
			}

		}
		else
		{
			// Invalid user id return a guest user
			return new User();
		}

	}
	// Not cookie, return a guest user
	return new User();
}
// Build the navigation links
function buildMenu()
{
	// Global variables
	global $page,$config,$user, $db;
	$menu_categories = array();
	// Include all the menu generation plugins
	$dir = dir(BASE_DIR . 'plugins');
	while (($entry = $dir->read()) !== false)
	{
		if (substr($entry, 0, 5) == "menu_")
			include BASE_DIR . "plugins/" . $entry;
	}
	return $menu_categories;
}

// Languages are not currently used
function loadLanguageFile($file, $lang)
{
	$lang = (!empty($lang)) ? preg_replace('#[\.\\\/]#', '', $lang) : 'English';

	if (!file_exists(BASE_DIR.'lang/'.$lang.'/'.$file))
		displayError('Invalid language file, please verify the language you selected is valid and try again');
	$language = new lang;

	require BASE_DIR.'lang/'.$lang.'/'.$file;

	return $language;

}

function scriptRequestFile()
{
	$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
	if (substr($path, -1) != '/')
		$path = $path.'/';

	$file = substr(urldecode($_SERVER['REQUEST_URI']), strlen($path));
	if (strpos($file, '?') !== false)
		$file = substr($file, 0, strpos($file, '?'));

	if ($file == '');
	{
		if (isset($page))
		{
			return 'page_' . $page . 'php';

		}
		return 'index.php';
	}

	return $file;
}

// Generate audio from the given string
function makeAudio($string)
{
	//echo $string;
	$wavs = array();
	for ($x = 0; $x < strlen($string); $x++)
	{
		$file = $string[$x];
		if ($string[$x] == '.')
			$file = "dot";
		if ($string[$x] == '@')
			$file = "at";

		

		$wavs[] = './audio/' . strtolower($file) . '.wav';
	}
	//print_r($wavs);
	//die();
	createWav($wavs);

}
// Creates a wave file and outputs it's content
// The origin of the snippit is unknown
// TODO try to track down origin for credit
function createWav($wavs)
{
	header("Content-type:audio/x-wav ");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header('Content-Disposition: attachment; filename=audio.wav');
	$wav_heads = join('/',array( 'H8ChunkID', 'VChunkSize', 'H8Format', 'H8Subchunk1ID', 
				'VSubchunk1Size', 'vAudioFormat', 'vNumChannels', 'VSampleRate',
                             'VByteRate', 'vBlockAlign', 'vBitsPerSample' ));
	$wav_data = '';
   
	foreach($wavs as $wav)
	{
       		$fp     = fopen($wav,'rb');
       		$wav_header = fread($fp,36);
       		$info   = unpack($wav_heads,$wav_header);
		// read optional extra stuff
		if($info['Subchunk1Size'] > 16)
		{	
			$wav_header .= fread($fp,($info['Subchunk1Size']-16));
		}
		// read SubChunk2ID
		$wav_header .= fread($fp,4);
		// read Subchunk2Size
		$wav_size  = unpack('vsize',fread($fp, 4));
		$wav_size  = $wav_size['size'];
		// read data
		$wav_data .= fread($fp,$wav_size);
	}
	echo $wav_header.pack('V',strlen($wav_data)).$wav_data;
	die();
}

// Error function
function displayError($error, $sqlerror = "")
{
	if ($sqlerror != "")
		$error .= '\n' . $sqlerror['error_msg'];
	$render = new gsRender(false);
	$render->assign('error', $error);
	echo $render->fetch('error.htm');
	die();
}

// Redirect mechanism
function redirect($message, $url = null)
{
	require_once("common.php");
	global $user,$config,$page;
	$render = new gsRender(false);
	$render->assign("page", $page);
	$render->assign("config", $config);
	$render->assign("user", $user);
	$render->assign('redirect', 'true');
	$render->assign('redirecturl',$url);
	$render->assign('redirect_message', $message);
	echo $render->fetch('header.htm');
	echo $render->fetch('core_redirect.htm');
	echo $render->fetch('footer.htm');
	die();
}
?>
