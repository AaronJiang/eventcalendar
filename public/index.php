<?php 

/*
 * Include necessary files
 */
include_once '../sys/core/init.inc.php';

/*
 * Load the calendar for January
 */
$cal = new Calendar($dbo, "2010-01-01 12:00:00");

/*
 * Include the header
 */
$page_title = "Events Calendar";
$css_files = array("style.css");
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
