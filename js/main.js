$(document).ready(function() {
	var isLoaded = false;
	
    $.fn.fullpage({
    	onLeave: function(index, direction) {
    		if (!isLoaded) return;
    		// if the user LEFT the 1st section, show the scroll up button
    		if (index === 1) {
    			$("#scroll-up").fadeIn();
    			$(".biography").fadeIn(2200);
    		}
    		
    		// if the user is GOING to the 1st page, hide the scroll up button
    		if (index === 2 && direction == "up") {
    			$("#scroll-up, .biography").fadeOut();
    		} 
    	},
    	afterLoad: function(anchorLink, index) {
    		isLoaded = true;
    		if (index !== 1) {
    			$("#scroll-up, .biography").show();
    		} else {
    			$("#scroll-up, .biography").hide();
    		}
    	}
    });
});


$("#contact-send").click(function() {
	var inputs = $(".contactframe").find("input[type='text'], textarea");
	var valid = true;
	inputs.each(function(index, el) {
		if (el.value.trim() == "") valid = false;
	});
	
	if (!valid) {
		alert("Please fill in every input");
		return;
	}
	
	$.post("contact.php", $(".contactframe [name]").serialize(), function(data) {
		var json = JSON.parse(data);
		alert(json.message);
	}).fail(function(data) {
		var json = JSON.parse(data);
		alert(json.message);
	});
});


/*$('body').slimScroll({
    railVisible: true,
    alwaysVisible: true,
    height: 'auto'
});*/

$("img").mousedown(function(){
    return false;
});


$(".menu-opener").click(function() {
  $(".menu-opener, .menu-opener-inner, .menu").toggleClass("active");
});



/*
$(document).ready(function(){
    $(".storenotextended").click(function(){
        $(".storenotextended").toggleClass("storeextended");
    });
});*/

