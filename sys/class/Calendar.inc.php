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
			$sql .= "WHERE `event_start` BETWEEN '$start_date'
					AND '$end_date' ORDER BY `event_start`";
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

	/**
	 * Returns a single event object
	 *
	 * @param int $id an event ID
	 * @return object the event object
	 **/
	private function _loadEventById($id)
	{
		/*
		 * If no ID is passed, return NULL
		 */
		if(empty($id))
		{
			return NULL;
		}

		/*
		 * Load the events info array
		 */
		$event = $this->_loadEventData($id);

		/*
		 * Return an event object
		 */
		if(isset($event[0]))
		{
			return new Event($event[0]);
		}
		else
		{
			return NULL;
		}	

	}
	
	/**
	 * Return HTML markup to display the calendar and events
	 * 
	 * @return string the calendar HTML markup
	 */
	public function buildCalendar()
	{
		/*
		 * Determine the calendar month and create an array of
		 * weekday abbreviations to label the calendar columns 
		 */
		$cal_month = date('F Y', strtotime($this->_useDate));
		$weekdays = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		
		/*
		 * Add a header to the calendar markup
		 */
		$html = "\n\t<h2>$cal_month</h2>";
		for($d=0, $labels=NULL; $d<7; ++$d)
		{
			$labels .= "\n\t\t<li>".$weekdays[$d]."</li>";
		}	
		$html .= "\n\t<ul class=\"weekdays\">".$labels."\n\t</ul>";
		
		/*
		 * Load events data
		 */
		$events = $this->_createEventObj();
		
		/*
		 * Create the calendar markup 
		 */ 
		$html .= "\n\t<ul>"; // Start a new unordered list
		for ( $i=1, $c=1, $t=date('j'), $m=date('m'), $y=date('Y');
		          $c<=$this->_daysInMonth; ++$i )
		{
		     /*
		      * Apply a "fill" class to the boxes occurring before
		      * the first of the month
		      */
		     $class = $i<=$this->_startDay ? "fill" : NULL;
		     
		     /*
		      * Add a "today" class if the current date matches
		      * the current date
		      */
		     if ( $c+1==$t && $m==$this->_m && $y==$this->_y )
		     {
		          $class = "today";
		     }
		     
		     /*
		      * Build the opening and closing list item tags
		      */
		     $ls = sprintf("\n\t\t<li class=\"%s\">", $class);
			 $le = "\n\t\t</li>";
			 
		     /*
		      * Add the day of the month to identify the calendar box
		      */
			 $event_info = NULL;
		     if ( $this->_startDay<$i && $this->_daysInMonth>=$c)
		     {
		     	/*
		     	 * Format events data
		     	 */
		     	 // clear the variable
		     	if ( isset($events[$c]) )
		     	{
		     		foreach ( $events[$c] as $event )
		     		{
		     			$link = '<a href="view.php?event_id='
		     					. $event->id . '">' . $event->title
		     					. '</a>';
		     			$event_info .= "\n\t\t\t$link";
		     		}
		     	}
		     	
		     	$date = sprintf("\n\t\t\t<strong>%02d</strong>",$c++);
		     }
		     else 
		     { 
		     	$date="&nbsp;"; 
		     }
		     
			/*
		     * If the current day is a Saturday, wrap to the next row
		     */
		    $wrap = $i!=0 && $i%7==0 ? "\n\t</ul>\n\t<ul>" : NULL;
		    
		    /*
		     * Assemble the pieces into a finished item
		     */
		    $html .= $ls . $date . $event_info . $le . $wrap;
		}
		/*
		 * Add filler to finish out the last week
		 */
		while ( $i%7!=1 )
		{
			$html .= "\n\t\t<li class=\"fill\">&nbsp;</li>";
			++$i;
		}
		
		/*
		 * Close the final unordered list
		 */
		$html .= "\n\t</ul>\n\n";

		/*
		 * Return the markup for output
		 */
		return $html;
	}	
	
	/**
	 * Displays a given event's information
	 *
	 * @param int $id the event ID	
	 * @return string markup to display the  event information
	 **/
	public function displayEvent($id)
	{
		/*
		 * Make sure an ID was passed
		 */
		if(empty($id))
		{
			return NULL;
		}

		/*
		 * Make sure the ID is an integer
		 */
		$id = preg_replace('/[^0-9]/', '', $id);

		/*
		 * Load the event data from the DB
		 */
		$event = $this->_loadEventById($id);

		/*
		 * Generate strings for the date, start, and end time
		 */
		$ts = strtotime($event->start);
		$date = date('F d, Y', $ts);
		$start = date('g:ia', $ts);
		$end = date('g:ia', strtotime($event->end));

		/*
		 * Generate and return the markup
		 */
		return "<h2>$event->title</h2>"
		    . "\n\t<p class=\"dates\">$date, $start&mdash;$end</p>"
		    . "\n\t<p>$event->description</p>";

	}
}
