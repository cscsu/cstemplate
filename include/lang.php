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

// Languages are currently unused
class lang {
	public static $language = array();
	public function __call($name,$arguments)
	{
		if (array_key_exists($name, self::$language))
			{
				if (array_key_exists($arguments[0], self::$language[$name]))
					return self::$language[$name][$arguments[0]];
				else
					return "Undefined language index";
			}
		else
			return "Undefined language file";
	}
	public static function load($name, $strings)
	{
		self::$language[$name] = $strings;
	}
}
?>