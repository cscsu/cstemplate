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
    
	include("./plugins/widget_" . $name . ".php");
    return $smarty->fetch("widget_" .$name . ".htm");
    
}

/* vim: set expandtab: */

?>
