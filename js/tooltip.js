this.tooltip = function(){
	/* CONFIG */
		xOffset = -20;
		yOffset = -200;
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
	/* END CONFIG */
	$("span.tooltip").hover(function(e){
		this.t = this.title;
		this.title = "";
                $("body").append("<span id='tooltip'>"+ this.t +"</span>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");
    },
	function(){
		this.title = this.t;
		$("#tooltip").remove();
    });
	$("span.tooltip").mousemove(function(e){
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});
};



// starting the script on page load
$(document).ready(function(){
	tooltip();
});