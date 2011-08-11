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

	// User class
	class User
	{
		// USer variables
		private $username = "";
		private $userid = -1;
		private $firstname = "";
		private $lastname = "";
		private $fullname = "";
		// Constructor
		public function __construct()
		{
			
				$args = func_get_args();

				$nargs = func_num_args();
			
				if ($nargs == 4)
			
				{
					$this->userid = intval($args[0]);
	
				$this->username = $args[1];
					$this->firstname = $args[2];
					$this->lastname = $args[3];
	
			}
		}

		// Function returns true or false based on whether or not the use is a guest
		public function isGuest()
		{
			if ($this->userid === -1)
				return true;
			return false;
		}
		// Returns concatenation of the first and last name
		public function getFullName()
		{
			return $this->firstname . " " . $this->lastname;
		}
		// Return the user id
		public function getUserID()
		{
			return $this->userid;
		}
		// Return the first name
		public function getFirstName()
		{
			return $this->firstname;
		}
		// Return the last name
		public function getLastName()
		{
			return $this->lastname;
		}
		// Return the username
		public function getUserName()
		{
			return $this->username;
		}
	}

?>
