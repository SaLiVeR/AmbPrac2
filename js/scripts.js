function var_check(element){
	if (element.val() == "")
	return false;
	else
	return true;
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

$(document).ready(function() {

			var body_ID = $("body").attr("id");
			
			var config = {
				 sensitivity: 7,
				 interval: 10,
				 over: hoverOver,
				 timeout: 200,
				 out: hoverOut
			};
			$("ul#topnav li").hoverIntent(config);
			
			if (body_ID == "home"){
				$('#slider').nivoSlider({
					effect: 'random', // Specify sets like: 'fold,fade,sliceDown'
					slices: 20, // For slice animations
					boxCols: 12, // For box animations
					boxRows: 5, // For box animations
					animSpeed: 500, // Slide transition speed
					pauseTime: 4000, // How long each slide will show
					startSlide: 0, // Set starting Slide (0 index)
					directionNav: true, // Next & Prev navigation
					directionNavHide: false, // Only show on hover
					controlNav: true, // 1,2,3... navigation
					controlNavThumbs: false, // Use thumbnails for Control Nav
					controlNavThumbsFromRel: false, // Use image rel for thumbs
					keyboardNav: true, // Use left & right arrows
					pauseOnHover: false, // Stop animation while hovering
					manualAdvance: false, // Force manual transitions
					captionOpacity: 1, // Universal caption opacity
					prevText: '', // Prev directionNav text
					nextText: '', // Next directionNav text
					randomStart: false // Start on a random slide
				});
				$("#new_postings_slides, #upcoming_events_slides").slides({
					preload: true,
					container: 'box_container',
					play: 10000,
					next: 'box_next',
					prev: 'box_prev',
					generatePagination: false				
				});
				$("#new_postings_slides").bind({
					mouseenter:function(){
						$(".arrows1").show();
					},
					mouseleave:function(){
						$(".arrows1").hide();
					}
				});
				$("#upcoming_events_slides").bind({
					mouseenter:function(){
						$(".arrows2").show();
					},
					mouseleave:function(){
						$(".arrows2").hide();
					}
				});
			}
			else if (body_ID == "register"){
				var username = $("#username");
				var email = $("#email");
				var password = $("#password");
				var re_password = $("#re_password");
				var first_name = $("#first_name");
				var last_name = $("#last_name");
				var title = $("#title");
				var area = $("#area");
				
				$("#submit").click(function(){
					// checking empty field
					if (var_check(username) && var_check(email) && var_check(password) && var_check(re_password)
					    && var_check(first_name) && var_check(last_name) && var_check(title) && var_check(area)){
						// if email is not empty, validate its format
						if(isValidEmailAddress(email.val())){
							$("#registerform").get(0).submit();
						}
						// invalid email format
						else {
							alert("email is invalid");
						}						
					}
					// at least one of the fields is empty
					else{
						alert("empty");
					}
					// prevents submit to submit the form
					return false;
				});	
			}
		});
		
//On Hover Over
function hoverOver(){
	$(this).find(".sub").stop().fadeTo('fast', 1).show(); //Find sub and fade it in
	$("a",this).addClass("hover");
	(function($) {
		//Function to calculate total width of all ul's
		jQuery.fn.calcSubWidth = function() {
			rowWidth = 0;
			//Calculate row
			$(this).find("ul").each(function() { //for each ul...
				rowWidth += $(this).width(); //Add each ul's width together
			});
		};
	})(jQuery); 

	if ( $(this).find(".row").length > 0 ) { //If row exists...

		var biggestRow = 0;	

		$(this).find(".row").each(function() {	//for each row...
			$(this).calcSubWidth(); //Call function to calculate width of all ul's
			//Find biggest row
			if(rowWidth > biggestRow) {
				biggestRow = rowWidth;
			}
		});

		$(this).find(".sub").css({'width' :biggestRow}); //Set width
		$(this).find(".row:last").css({'margin':'0'});  //Kill last row's margin

	} else { //If row does not exist...

		$(this).calcSubWidth();  //Call function to calculate width of all ul's
		$(this).find(".sub").css({'width' : rowWidth}); //Set Width

	}
}
//On Hover Out
function hoverOut(){
	$(this).find(".sub").stop().fadeTo('fast', 0, function() { //Fade to 0 opactiy
		$(this).hide();  //after fading, hide it
	});
	$("a",this).removeClass("hover");
}