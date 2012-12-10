<?php 
/*
 * Include the necessary configuration info
 */
include_once '../sys/config/db-cred.inc.php';

/*
 * Define constants for configutation info
 */
foreach($C as $name=>$val)
{
	define($name, $val);
}	

/*
 * Create a PDO object
 */
$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
$dbo = new PDO($dsn, DB_USER, DB_PASS);

/*
 * Define the autoload function for classes
 */ 
function __autoload($class)
{
	$filename = "../sys/class/".$class.".inc.php";
	if(file_exists($filename))
	{
		include_once $filename;
	}	
}
