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

$api_key = "148168981934263";
$api_secret = "207689dc5fcc3bfd36f8623fba33a78b";

function generatePermissionURL ($baseurl)
{
	global $api_key;
	global $api_secret;
	$base = "https://www.facebook.com/dialog/oauth?";
	$key = "client_id=" . $api_key;
	$next = "&redirect_uri=" . $baseurl . "/index.php?page=social%26facebook=1";
	$permissions = "&scope=manage_pages,offline_access,publish_stream,read_stream";
	return $base . $key . $next . $permissions;
}


function generateTokenURL ($baseurl,$codestring)
{
	global $api_key;
	global $api_secret;
	$base = "https://graph.facebook.com/oauth/access_token?";
	$key = "client_id=" . $api_key;
	$next = "&redirect_uri=" . $baseurl . "/index.php?page=social%26facebook=1";
	$secret = "&client_secret=" . $api_secret;
	$code = "&code=" . $codestring;
	return $base . $key . $next . $secret . $code;
}


function makeTokenRequest($requestURL)
{
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestURL);
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_GET, 1);
        $response = curl_exec($curl);
	$response = substr($response,13);
	return $response;
}

function makePost($token,$user,$message, $name="",$link ="", $caption="",$description="", $picture = "")
{

	global $api_key;
	global $api_secret;
	$url = "https://graph.facebook.com/" . $user . "/feed";
	$ch = curl_init();
	$attachment =  array(   'access_token'  => $token,                        
                    'message' => $message,

                );

	if ($name != "")
		$attachment['name'] = $name;

	if ($link != "")
		$attachment['link'] = $link;

	if ($caption != "")
		$attachment['caption'] = $caption;

	if ($description != "")
		$attachment['description'] = $description;

	if ($picture != "")
		$attachment['picture'] = $picture;
	
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
	$result = curl_exec($ch);
}

function getTokenPage($token)
{

	global $api_key;
	global $api_secret;
	$url = "https://graph.facebook.com/me/accounts?access_token=" . $token;
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_GET, 1);
	$result = curl_exec($ch);
	$page = getJSONField("id",$result);
	$ntoken = getJSONField("access_token",$result);


	return array($ntoken,$page);
}

function getJSONField($field,$json)
{
	$str = substr($json, strpos($json, "\"".$field."\""));
	if (strpos($str,",") > 0)
	{
		$str = substr($str, 0, strpos($str,","));
	}
	if (strpos($str,"}") > 0)
	{
		$str = substr($str, 0, strpos($str,"}"));
	}
	$ntoken = substr($str,strpos($str,":\"")+2);
	$ntoken = substr($ntoken,0,strlen($ntoken)-1);
	return $ntoken;
}



?>
