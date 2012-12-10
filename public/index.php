<?php 

/*
 * Include necessary files
 */
include_once '../sys/core/init.inc.php';

/*
 * Load the calendar for January
 */
$cal = new Calendar($dbo, "2012-01-01 12:00:00");

if(is_object($cal))
{
	var_dump($cal);
}