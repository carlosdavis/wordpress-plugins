function newQuote(categories, id, url, linkphrase, sequence)
{
	
	jQuery(document).ready
	(
		
		function($)
		{
			$("div.stray_quote-" + id).fadeOut('slow');
			$("div.stray_quote-" + id).empty(); //needed to avoid the creation of two divs one inside the other
			
			$("div.stray_quote-" + id).html('<div align="center">loading...</div>').fadeIn('slow', function () {
																											 
				$.ajax({
						type:	"POST",
						url:	url + "/inc/stray_ajax.php",
						data:	"action=newquote&categories=" + categories + "&sequence=" + sequence + "&linkphrase=" + linkphrase + "&widgetid=" + id,
						success: function(html)
						{	
							//needed to avoid the creation of two divs one inside the other
							if ($("div.stray_quote-" + id).parent().is("div.stray_quote-" + id)==false) { 
								$("div.stray_quote-" + id).html(html);
							}
						}
				});
				
		  });
			
		}
	)
}
