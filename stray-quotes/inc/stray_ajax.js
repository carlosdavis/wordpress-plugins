jQuery(document).ready(function(){});  //needed because FF is stupid

function newQuote(categories, linkphrase, id, strayurl, multi, offset, sequence, timer, disableaspect, loading, contributor){
	
	jQuery(document).ready
	(
		function($){
			
				var divheight = $("div.stray_quote-" + id).height();
				$("div.stray_quote-" + id).height(divheight/2);
				$("div.stray_quote-" + id).css('text-align','center');
				$("div.stray_quote-" + id).css('padding-top',divheight/2);
				$("div.stray_quote-" + id).fadeOut('slow');
				$("div.stray_quote-" + id).html(loading).fadeIn('slow', function () {
																												 
					$.ajax({
							type: "POST",
							url: strayurl + "inc/stray_ajax.php",
							data: "action=newquote&categories=" + categories + "&sequence=" + sequence + "&linkphrase=" + linkphrase + "&widgetid=" + id + "&multi=" + multi + "&offset=" + offset + "&disableaspect=" + disableaspect + "&timer=" + timer + "&contributor=" + contributor,
							success: function(html){
								$("div.stray_quote-" + id).css('padding-top',null);
								$("div.stray_quote-" + id).css('height', null);
								$("div.stray_quote-" + id).after(html).remove();
							}
					});
			  });
				
		}
	)
}