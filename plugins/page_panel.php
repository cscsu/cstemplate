<?php
$render->assign("pageinfo","");
$render->assign("pageid","page1");

if ($user->isGuest())
{

	redirect("You must be logged in");
}
?>
