<?php 

/**
 * Builds and manipulates an events calendar
 * 
 * @author Jason Lengstorf
 * @author Aaron Jiang
 */
class Calendar extends DBConnect
{
	/**
	 * The date from which the calendar shoud be built
	 * 
	 * Stored in YYYY-MM-DD HH:MM:SS format
	 * 
	 * @var String the date to use for the calendar
	 */
	private $_useDate;
	
	/**
	 * The month for which the calendar is being built
	 * 
	 * @var int the month being used
	 */
	private $_m;
	
	/**
	 * The year for which the month's start day is selected
	 * 
	 * @var int the year being used
	 */
	private $_y;
	
	/**
	 * The number of days in the month being used
	 * 
	 * @var int the number of days in the month
	 */
	private $_daysInMonth;
	
	/**
	 * The index of the day of the month starts on (0-6)
	 * 
	 * @var int the day of the week the month starts on
	 */
	private $_startDay;
	
	/**
	 * Create a database object and store relevant data
	 * 
	 * @param object $dbo a database object 
	 * @param string $useDate the date to use to build the calendar
	 * @return void
	 */
	public function __construct($dbo=NULL, $useDate=NULL)
	{
		/*
		 * Call the parent constructor to check for
		 * a database object
		 */
		parent::__construct($dbo);
	}
}
