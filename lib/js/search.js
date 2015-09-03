(function($){
	$(document).ready(function(){
		$("#submitallpost").click(function(){
		$("#loader").show();
		$.ajax({
		  url: ajaxurl,
          type:'POST',
          data:{action:'home_page_import_all_data', submitallpost:'1'},
          success: function(responce) {
		  $("#loader").hide();
		  /* alert(responce);
		  return false; */
		  
            if(responce == 'error')
			{
				alert("There are some problem occured in index all post");
				return false;
			}
			else
			{
				alert("Your all posts has been successfully import");
				return false;
			}
          }
        });
			return false;
		})
		
		$("#submit_methi_authentication").click(function(){
			//alert("form submit");
			var secretkey = $("#appbase_secret_token").val();
			
			if(secretkey == "" || secretkey == null)
			{
				alert("Please insert your secret key");
				return false;
			}
			else
			{
				return true;
			}
			
		})
	});
})(jQuery);

