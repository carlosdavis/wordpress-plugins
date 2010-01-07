jQuery(document).ready(function () {
	jQuery("#disableexplanation").change(function() {
		if (jQuery("#disableexplanation").is(':checked')) {
			jQuery("p.desc").css("display","none");
		} else {
			jQuery("p.desc").css("display","block");
		}
	}).change();
});