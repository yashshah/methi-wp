(function($){
	$(document).ready(function(){
		var searchboxlength = $("input[name=s]").length;
		//alert(searchboxlength);
		if(searchboxlength > 0)
		{
			$("input[name=s]").addClass("appbase_external_search");
		}
	});
})(jQuery);

