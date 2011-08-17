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

// Include the Smary rendering engine
require_once('Smarty.class.php');

// Extend the smarty base class
class gsRender extends Smarty {

    // Constructor
    function gsRender($caching = null)
    {
        // first, get a native Smarty object
        $this->Smarty();
        $this->compile_check = true;
	$this->debugging = true;
       
        // begin holder tag (be nice to others)
        $this->left_delimiter = '<!--[';
        // end holder tag
        $this->right_delimiter = ']-->';

 	  $this->register_prefilter('handle_delimiters');
        //---- Cache handling -------------------------------------------------
        // use HTML cache system?
        if (isset($caching) && is_bool($caching)) {
            $this->caching = $caching;
        } else {
            $this->caching = false;
        }
	$this->catching = false;
         // HTML cache directory
        $this->cache_dir = BASE_DIR . '/cache/gsRender';

        // don't use subdirectories when creating compiled/cached templates
        // this works better in a hosted environment
        $this->use_sub_dirs = false;

        // cache directory (compiled templates)
        $this->compile_dir = BASE_DIR . '/cache/gsRender/gsRender_compiled';

        // template directory
        $this->template_dir = BASE_DIR . '/templates';

    }
    function fetch($filename)
    {
	global $lang, $config;
	$this->assign('lang', $lang);
	$this->assign('config', $config);
	return parent::fetch($filename);
    }

}

function handle_delimiters($tpl_source, &$smarty)
{
		$tmp = preg_replace("/<!--[\s]*\[/U",'<!--[',$tpl_source);
		return preg_replace("/\][\s]*-->/U",']-->',$tmp);
}
