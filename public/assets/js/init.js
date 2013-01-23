//Make sure the document is ready before executing scripts
jQuery(function($){
	
	//File to which ajax requests should be sent
	var processFile = "assets/inc/ajax.inc.php";
	
	//Functions to manipulate the modal window
	var fx = {
			
		//Check for a modal window and returns it, or
		//else creates a new one and returns that
		"initModal" : function(){
				//If no element are matched, the length
				//property will return 0
				if( $(".modal-window").length == 0)
				{
					//Creates a div, adds a class, and 
					//appends it to the body tag
					return $("<div>")
							.hide()
							.addClass("modal-window")
							.appendTo("body");
				}
				else
				{
					//Returns the modal window if one 
					//already exists in the DOM
					return $(".modal-window");
				}	
		},

		//Adds the window to the markup and fades it in
		"boxin" : function (data, modal){
			//Creates an overlay for the site, adds
			//a class and a click event handler, then
			//appends it to the body element
			$("<div>")
				.hide()
				.addClass("modal-overlay")
				.click(function(event){
					//Removes event
					fx.boxout(event);
				})
				.appendTo("body");

			//Loads data into the modal window and
			//appends it to the body element
			modal
				.hide()
				.append(data)
				.appendTo("body");

			//Fades	in the modal window and overlay
			$(".modal-window, .modal-overlay")
				.fadeIn("slow");
		},
		
		//Fades out the window and removes it from the DOM
		"boxout" : function(event){
			//If an event was triggered by the element
			//that called this function, prevents the 
			//default action from firing
			if( event!=undefined )
			{
				event.preventDefault();
			}
			
			//Removes the active class from all links
			$("a").removeClass("active");
			
			//Fade out the modal window, them removes 
			//it from the DOM entirely
			$(".modal-window, .modal-overlay") .fadeOut("slow",function(){
							$(this).remove();
						})
		},

		//Adds a new event to the markup after saving
		"addevent" : function(data, formData){
			//Converts the query string to an object
			var entry = fx.deserialize(formData),
			
			//Makes a date object for current month
			cal = new Date(NaN),
			
			//Makes a date object for the new event
			event = new Date(NaN),
			
			//Extracts the calendar month from the H2 ID
			cdata = $("h2").attr("id").split('-'),
			
			//Extracts the event day, month, and year
			date = entry.event_start.split(' ')[0];
			
			//Splits the event date into pieces
			edata = date.split('-');
			
			//Sets the date for the calendar date object
			cal.setFullYear(cdata[1], cdata[2], 1);
			
			//Sets the date for the event date object
			event.setFullYear(edata[0], edata[1], edata[2]);
			
			//Since the date object is created using
			//GMT, then adjusted for the local timezone,
			//adjust the offset to ensure a proper date
			event.setMinutes(event.getTimezoneOffset());
			
			//If the year and month match, start the process
			//of adding the new event to the calendar
			if(cal.getFullYear() == event.getFullYear()
					&& cal.getMonth() == event.getMonth())
			{
				//Gets the day of the month for event
				var day = String(event.getDate());
				
				//Adds a leading zero to 1-digit days
				day = day.length==1 ? "0"+day : day;
				
				//Adds the new date link
				$("<a>")
					.hide()
					.attr("href", "view.php?event_id="+data)
					.text(entry.event_title)
					.insertAfter($("strong:contains("+day+")"))
					.delay(1000)
					.fadeIn("slow");
			}
			
		},
			
		//Deserializes the query string and returns
		//an event object
		"deserialize" : function(str){
			//Breaks apart each name-value pair
			var data = str.split("&"),

			//Declares variables for use in the loop
			pairs = [], entry = {}, key, val;

			//Loops through each name-value pair
			for(x in data)
			{
				//Splits each pair into an array
				pairs = data[x].split("=");

				//The first element is the name
				key = pairs[0];

				//Second element is the value
				val = pairs[1];

				//Store each value as an object property
				entry[key] = fx.urldecode(val);
			}

			return entry;
		},

		//Decodes a query string value
		"urldecode" : function(str){
			//Convert plus signs to spaces
			var converted = str.replace(/\+/g, ' ');

			//Converts any encoded entities back
			return decodeURIComponent(converted);
		}
	};
	
	
	//Pulls up events in a modal window
	$("li>a").live("click", function(event){
		event.preventDefault();
		
		//Adds an "active" class to the link
		$(this).addClass("active");
		
		//Get the query string from the link href
		var data = $(this).attr("href")
						  .replace(/.+?\?(.*)$/, "$1");
		
		//Check if the modal window exists and 
		//select it, or creates a new one
		modal = fx.initModal();
		
		//Create a button to close the window
		$("<a>").attr("href", "#")
				.addClass("modal-close-btn")
				.html("&times;")
				.click(function(event){
					//Removes modal window
					fx.boxout(event);
				})
				.appendTo(modal);

		//Loads the event data from the DB
		$.ajax({
			type: "POST",
			url: processFile,
			data: "action=event_view&"+data,
			success: function(data){
				fx.boxin(data, modal);
				},
			error: function(msg){
				modal.append(msg);
				}
		});
	})

	//Display the edit form as a modal window
	$(".admin").live("click", function(event){
		//Prevents the form from submitting
		event.preventDefault();

		//Loads the action for the processing file
		var action = "edit_event";

		//Loads the editing form and displays it
		$.ajax({
			type : "POST",
			url : processFile,
			data : "action="+action,
			success : function(data){
				//Hide the form
				var form = $(data).hide(),

				//Make sure the modal window exists
				modal = fx.initModal();

				//Call the boxin function to create
				//the modal overlay and fade it in
				fx.boxin(null, modal);

				//Load the form into the window,
				//fades in the content, and adds
				//a class to the form
				form 
					.appendTo(modal)
					.addClass("edit-form")
					.fadeIn("slow");
			},
			error : function(msg){
				alert(msg);
			}
		});
	});

	//Edits events without reloading
	$(".edit-form input[type=submit]").live("click", function(event){
		//Prevents the default form action from executing
		event.preventDefault();

		//Serializes the form data for use with $.ajax()
		var formData = $(this).parents("form").serialize();

		//Sends the data to the processing file
		$.ajax({
			type : "POST",
			url : processFile,
			data : formData,
			success : function(data){
				//Fades out the modal window
				fx.boxout();

				//Adds the event to the calendar
				fx.addevent(data, formData);
			},
			error : function(msg){
				alert(msg);
			}
		});
	});

	//Make the cancel button on editing forms behave like the
	//close button and fade out modal windows and overlays
	$(".edit-form a:contains(cancel)").live("click", function(event){
		fx.boxout(event);
	});

});