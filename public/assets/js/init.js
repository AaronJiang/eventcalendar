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
			$(".modal-window") .fadeOut("slow",function(){
							$(this).remove();
						})
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
				modal.append(data);
				},
			error: function(msg){
				modal.append(msg);
				}
		});
	})
})
