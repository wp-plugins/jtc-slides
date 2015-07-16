	var curJtcSlide = 1;
	
	 jQuery("document").ready(function(){
		jQuery(".jtcSlideShowBoxInner a").click(function(){
			var jtcSlideNum = jQuery(this).data("jtcSlide");
			var jtcSlideShowIdNum = jQuery(this).data("jtcSlideShowId");
			jQuery("#jtcSlideShowWrapper"+jtcSlideShowIdNum+" .jtcSlide").hide();
			jQuery("#jtcSlideShowWrapper"+jtcSlideShowIdNum+" #jtcSlide-"+jtcSlideNum).fadeIn();
			jQuery("#jtcSlideShowWrapper"+jtcSlideShowIdNum+" .jtcSlideShowBox .jtcSlideShowBoxInner a").removeClass("selected");
			jQuery("#jtcSlideShowWrapper"+jtcSlideShowIdNum+" #jtcSlideShowBox-"+jtcSlideNum+" .jtcSlideShowBoxInner a").addClass("selected");
		});
	 
	 });
	 

	
