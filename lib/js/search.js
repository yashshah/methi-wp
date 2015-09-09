(function($){
	$(document).ready(function(){
		$("#submitallpost").click(function(){
		
		var appbasesecretkey = $("#appbase_secret_token").val();
		
		if(appbasesecretkey == "" || appbasesecretkey == null)
		{
			alert("Please insert your secret key");
			return false;
		}
		else
		{
		
		$("#import_record_process").show();
		$.ajax({
		  url: ajaxurl,
          type:'POST',
          data:{action:'methiAuth', submitallpost:'1', appbasesecretkey:appbasesecretkey },
		  xhr: function() {
			   var xhr = new window.XMLHttpRequest();
			   xhr.addEventListener("progress", function(e) {
					var countstring = e.currentTarget.responseText;
					//console.log(countstring);
					var postcount = countstring.substr(countstring.lastIndexOf('/')+1).slice(0,-1);
					//console.log(postcount);
					var total = countstring.substr(0, countstring.indexOf('/'));
					$("#postcountprogress").html(postcount);
				
				   var percentComplete = (parseInt(postcount) / parseInt(total)) * 100;
				   var finalpercentage = parseInt(percentComplete)+"%";
				   //console.log(finalpercentage);
				   $(".actualprocess").css("width",finalpercentage);
				   $("#precentageprogress").html(parseInt(percentComplete));
				   
			   }, false);

			   return xhr;
			},
          success: function(responce) {
		  
		  $("#import_record_process").hide();
		   /* alert(responce);
		  return false;  */ 
		  
		  var patt = new RegExp("error");
		  var finderror = patt.test(responce);
		  
            if(finderror == true)
			{
				alert("There are some problem occured in index all post");
				return false;
			}
			else
			{
				alert("Your all posts has been successfully import");
				window.location.href = adminurl + '?page=settings';
				return false;
			}
          }
        });
			return false;
		}
		});
		
		$("#reindexallpost").click(function(){
		$("#import_record_process").show();
		var appbasesecretkey = $("#appbase_secret_token").val();

		$.ajax({
		  url: ajaxurl,
          type:'POST',
          data:{action:'reindex_all_data', reindexallpost:'1',},
		  xhr: function() {
			   var xhr = new window.XMLHttpRequest();
			   xhr.addEventListener("progress", function(e) {
					var countstring = e.currentTarget.responseText;
					//console.log(countstring);
					var postcount = countstring.substr(countstring.lastIndexOf('/')+1).slice(0,-1);
					//console.log(postcount);
					var total = countstring.substr(0, countstring.indexOf('/'));
					$("#postcountprogress").html(postcount);
				
				   var percentComplete = (parseInt(postcount) / parseInt(total)) * 100;
				   var finalpercentage = parseInt(percentComplete)+"%";
				   //console.log(finalpercentage);
				   $(".actualprocess").css("width",finalpercentage);
				   $("#precentageprogress").html(parseInt(percentComplete));				   
			   }, false);

			   return xhr;
			},
          success: function(responce) {
		  $("#import_record_process").hide();
		  /*  alert(responce);
		  return false;  */
		  
          var patt = new RegExp("error");
		  var finderror = patt.test(responce);
		  
            if(finderror == true)
			{
				alert("There are some problem occured in index all post");
				return false;
			}
			else
			{
				alert("Your all posts has been successfully reindex");
				return false;
			}
          }
        });
			return false;
		});
		
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

