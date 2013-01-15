<?php 
/*
 * Include necessary files
 */
include_once '../sys/core/init.inc.php';

/*
 * If the user is not logged in, send them to the main file
 */
if(!isset($_SESSION['user']))
{
	header("Location: ./");
	exit();	
}	

/*
 * Output the header
 */
$page_title = "Add/Edit Event";
$css_files = array("bootstrap.min.css","style.css","admin.css");
include_once 'assets/common/header.inc.php';

/*
 * Load the calendar
 */
$cal = new Calendar($dbo);

echo "<div id='content'>";
echo $cal->displayForm();
echo "</div>";

include_once 'assets/common/footer.inc.php';
