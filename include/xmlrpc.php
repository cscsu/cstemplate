
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

//XML RPC is currently unused
	class xmlrpcRequest
	{
		var $message;
		var $client;
		var $result;
		var $isFault = false;
		function xmlrpcRequest($server, $method)
		{
			//TODO Load server info from config
			$this->client = new xmlrpc_client("", $server['host'], $server['port']);
			if ($server['auth'] != "")
			{
				$this->setAuth($server['auth']);
			} 
			//$this->client->no_multicall = true;
			if (is_array($method))
			{
				$this->isFault = array();
				$this->message = array();
				for ($x = 0; $x < count($method); $x++)
				{
					$this->message[] = new xmlrpcmsg($method[$x]);
				}
			}
			else
			{
				$this->message = new xmlrpcmsg($method);
			}
		}
		function setAuth($authString)
		{
			$this->client->setCookie("auth",$authString);
		}
		function addParam($value, $type, $message = "")
		{
			if ($message == "")
			{
				$this->message->addParam(new xmlrpcval($value, $type));
				return;
			}
			if (is_array($this->message))
			{
				for ($x = 0; $x < count($this->message); $x++)
				{
					if ($this->message[$x] == $message)
						$this->message[$x]->addParam(new xmlrpcval($value, $type));
				}
			}
			else
			{
				$this->addParam($value,$type);
			}
		}

		function makeRequest()
		{
			$this->result = $this->client->send($this->message);
			if (is_array($this->result))
			{
				$ret = array();
				for ($x = 0; $x < count($this->result); $x++)
				{
					if (!$this->result[$x]->faultCode())
					{
						$ret[] = new xmlrpcResult($this->result[$x]->value());
						$this->isFault[] = false;
					}
					else
					{	
					
						$this->isFault[] = true;
						$ret[] = false;
					}
				}
				return $ret;
			}
			else
			{
				if (!$this->result->faultCode())
				{
					$ret = new xmlrpcResult($this->result->value());
					$this->isFault = false;
					return $ret;
				}
				else
				{
					$this->isFault = true;
					return false;
				}
			}
		}
		function faultCode($x = -1)
		{
			if ($x == -1)
				return $this->result->faultCode();
			else
				return $this->result[$x]->faultCode();
		}
		function faultString($x = -1)
		{
			if ($x == -1)
				return $this->result->faultString();
			else
				return $this->result[$x]->faultString();
		}

	}
	class xmlrpcResult
	{
		var $isArray = false;
		var $isStruct = false;
		var $isScalar = false;
		var $value;
		//var $resultVal;
		function xmlrpcResult($resultVal)
		{
			//$this->resultVal = $resultVal;
			//echo "balls";
			//print_r($this->resultVal);
			//echo "<br><br>";
			$this->_parseVal($resultVal);
		}
		private function _parseVal(xmlrpcval $rpcval)
		{
			$type = $rpcval->kindOf();
			//echo $type;
			switch ($type)
			{
				case "array":
					$this->isArray = true;
					for ($x = 0; $x < $rpcval->arraySize(); $x++)
					{
						$this->value = array();
						$this->value[] = new xmlrpcResult($rpcval->arrayMem($x));
					}
					break;
				case "struct":
					$this->isStruct = true;
					$rpcval->structReset();
					$this->value = array();
					while (list($key, $v) = $rpcval->structEach())
					{
						$this->value[$key] = new xmlrpcResult($v);
					}
					
					break;
				case "scalar":
					$this->isScalar = true;
					$this->value = $rpcval->scalarVal();
					break;
			}
		}
		public function __toString()
		{
			if ($this->isScalar)
				return (string)$this->value;
			else
				return "Non Scalar Type";
		}
	}
?>