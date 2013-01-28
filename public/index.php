<?php 

/*
 * Include necessary files
 */
include_once '../sys/core/init.inc.php';

/*
 * Load the calendar for January
 */
$cal = new Calendar($dbo, date('Y-m-01 H:i:s'));

/*
 * Include the header
 */
$page_title = "Events Calendar";
$css_files = array("bootstrap.min.css","style.css","admin.css","ajax.css");
include_once 'assets/common/header.inc.php';

/*
 * Display the calendar HTML
 */
echo "<div id='content'>";
echo $cal->buildCalendar();
echo "</div>";

/*
 * Include the footer
 */
include_once 'assets/common/footer.inc.php';
