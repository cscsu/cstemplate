<?php

/**
 * Smarty {widget} function plugin
 *
 * Type:     function<br>
 * Name:     widget<br>
 * Purpose:  include a widget block in the page
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_widget($params, &$smarty)
{
    $name = (isset($params['name'])) ? $params['name'] : 'default';
    
    if (!preg_match("/^[a-zA-Z0-9_]+$/i",$name))
    {
	return "";
    }


    if (file_exists("./plugins/widget_" . $name . ".php"))
    {
    	include("./plugins/widget_" . $name . ".php");
    }
    if ($smarty->template_exists("widget_" .$name . ".htm"))
    {
        return $smarty->fetch("widget_" .$name . ".htm");
    }
    else if ($smarty->template_exists("content/widget_" .$name . ".htm"))
    {
	return $smarty->fetch("content/widget_" .$name . ".htm");
    }
    else
    {
      return "";
    }
    
}

/* vim: set expandtab: */

?>
