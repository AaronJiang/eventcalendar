<?php 

/**
 * Manages administrative actions
 * 
 * @author Jason Lengstorf
 * @author Aaron Jiang
 */
class Admin extends DBConnect
{
	/**
	 * Determines the length of the salt to us in hashed passwords
	 * 
	 * @var int the length of the password salt to use
	 */
	private $_saltLength = 7;
	
	/**
	 * Stores or creates a DB object and sets length
	 * 
	 * @param object $db a database object
	 * @param int $saltLength length of the password hash
	 */
	public function __construct($db=NULL, $saltLength=NULL)
	{
		parent::__construct($db);
		/*
		 * If an int was passed, set the length of the salt
		 */
		if(is_int($saltLength))
		{
			$this->_saltLength = $saltLength;
		}	
	}
	
	/**
	 * Check login credentials for a valid user
	 * 
	 * @return mixed TRUE on success, message on error
	 */
	public function processLoginForm()
	{
		/*
		 * Fails if the proper action was not submitted
		 */
		if($_POST['action']!='user_login')
		{
			return "Invalid action supplied for process ";
		}	
		
		/*
		 * Escapes the user input for security
		 */
		$uname = htmlentities($_POST['uname'], ENT_QUOTES);
		$pword = htmlentities($_POST['pword'], ENT_QUOTES);
		
		/*
		 * Retrieves the matching info from the DB if it exists
		 */
		$sql = "SELECT `user_id`, `user_name`, `user_email`, `user_pass 
				FROM `users` WHERE `user_name` = :uname LIMIT 1";
		
		try
		{
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':uname', $uname, PDO::PARAM_STR);
			$stmt->execute();
			$user = array_shift($stmt->fetchAll());
			$stmt->closeCursor();
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
		
		/*
		 * Fails if username doesn't match a DB entry
		 */
		if(!isset($user))
		{
			return "Your username or password is invalid.";
		}
		
		/*
		 * Get the hash of the user-supplied password
		 */
		//$hash = $this->_getSaltedHash($pword, $user['user_pass']);
		$hash = $this->_getSaltedHash($pword);
		
		/*
		 * Checks if the hashed password matches the stored hash
		 */
		if($user['user_pass']==$hash)
		{
			/*
			 * Stores user info in the session as an array
			 */
			$_SESSION['user'] = array(
					'id' => $user['user_id'],
					'name' => $user['user_name'],
					'email' => $user['user_email']
			);
			return TRUE;
		}
		/*
		 * Fails if the passwords don't match
		 */
		else
		{
			return "Your username or password is invalid.";
		}
		
	}
	
	/**
	 * Generates a salted hash of a supplied string
	 * 
	 * @param string $string to be hashed
	 * @param string $salt extract the hash from here
	 * @return string the salted hash
	 */
	private function _getSaltedHash($string, $salt=NULL)
	{
		/*
		 * Generate a salt if no salt is passed
		 */
		if($salt==NULL)
		{
			$salt = substr(md5(time()), 0, $this->_saltLength);
		}
		/*
		 * Extract the salt from the string if one is passed
		 */
		else 
		{
			$salt = substr($salt, 0, $this->_saltLength);	
		}
		
		return $salt.sha1($salt.$string);
	}
	
	public function testSaltedHash($string, $salt=NULL)
	{
		return $this->_getSaltedHash($string, $salt);
	}
	
}