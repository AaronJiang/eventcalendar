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
		
		/*
		 * Gather and store data relevant to the month
		 */
		if(isset($useDate))
		{
			$this->_useDate = $useDate;
		}
		else
		{
			$this->_useDate = date('Y-m-d H:i:s');
		}
		
		/*
		 * Convert to a timestamp, then determine the month
		 * and year to use when building the calendar
		 */
		$ts = strtotime($this->_useDate);
		$this->_m = date('m',$ts);
		$this->_y = date('Y',$ts);
		
		/*
		 * Determine how many days are in the month
		 */
		$this->_daysInMonth = cal_days_in_month(
					CAL_GREGORIAN,
					$this->_m,
					$this->_y
				);
		
		/*
		 * Determine what weekday the month starts on
		 */
		$ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
		$this->_startDay = date('w',$ts);
	}
	
	/**
	 * Loads event info into an array
	 * 
	 * @param int $id an optional event ID to filter results
	 * @return array an array of events from the database
	 */
	private function _loadEventData($id=NULL)
	{
		$sql = "SELECT * FROM `events` ";
		
		/*
		 * If an event ID is supplied, add a WHERE clause
		 * so only that event is returned. Otherwise, load
		 * all events for the month in use
		 */
		if(isset($id))
		{
			$sql .= "WHERE `event_id`=:id LIMIT 1";
		}
		else
		{
			/*
			 * Find the first and last days of the month
			 */
			$start_ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
			$end_ts = mktime(23, 59, 59, $this->_m+1, 0, $this->_y);
			$start_date = date('Y-m-d H:i:s', $start_ts);
			$end_date = date('Y-m-d H:i:s', $end_ts);
			
			/*
			 * Filter events to only those happening in the currently
			 * selected month
			 */
			$sql .= "WHERE `event_start` BETWEEN `$start_date`
					AND `$end_date` ORDER BY `event_start`";
		}
		
		try
		{
			$stmt = $this->db->prepare($sql);
			
			/*
			 * Bind the parameter if an ID was passed
			 */
			if(isset($id))
			{
				$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			}
			
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			return $results;	
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}	
	
	/**
	 * Load all events for the month into an array
	 * 
	 * @return array events info
	 */
	private function _createEventObj()
	{
		/*
		 * Load the events array
		 */
		$arr = $this->_loadEventData();
		
		/*
		 * Create a new array, then organize the events 
		 * by the day of the month on which they occur
		 */
		$events = array();
		foreach($arr as $event)
		{
			$day = date('j', strtotime($event['event_start']));
			try 
			{
				$events[$day][] = new Event($event);
			}	
			catch(Exception $e)
			{
				die($e->getMessage());
			}
		}
		return $events;
	}
}
