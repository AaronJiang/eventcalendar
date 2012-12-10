<?php 
/**
 * Database actions (DB access, validation, etc.)
 *
 * @author Jason Lengstorf
 * @author Aaron Jiang
 */
class DBConnect
{
	/**
	 * Store a database object 
	 * 
	 * @var object A databse object
	 */
	protected $db;
	
	/**
	 * Check for a DB object or creates one if none exists
	 * 
	 * @param object $dbo A database object
	 */
	
	protected function __construct($dbo=NULL)
	{
		if(is_object($dbo))
		{
			$this->db = $dbo;
		}
		else 
		{
			//Constants are defined in /sys/config/db-cred.inc.php
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			try
			{
				$this->db = new PDO($dsn, DB_USER, DB_PASS);	
			}	
			catch(Exception $e)
			{
				die($e->getMessage());	
			}
		}
	}
}
