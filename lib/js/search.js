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
		$("#submitallpost").prop('disabled', true);
		$.ajax({
		  url: ajaxurl,
          type:'POST',
          data:{action:'methiAuth', submitallpost:'1', appbasesecretkey:appbasesecretkey },
		  beforeSend: function() {
                t = setTimeout("updateprogressstatus()", 2000);
            },
          success: function(responce) {
		  $("#import_record_process").hide();
		  $(".indexpostbutton").removeAttr("disabled");
			jQuery("#postcountprogress").html("0");
			jQuery(".actualprocess").css("width","0%");
			jQuery("#precentageprogress").html(parseInt("0"));
			$("#submitallpost").prop('disabled', false);
		  var patt = new RegExp("error");
		  var finderror = patt.test(responce);
            if(finderror == true)
			{
				alert("We were not able to sync your posts. Shoot us an email at search@methi.io with your magic key and wordpress version, we will look into it right away!");
				window.location.href = adminurl + '?page=methi_search';
				return false;
			}
			else
			{
				alert("All posts have been synced. Methi is live!");
				window.location.href = adminurl + '?page=methi_search';
				return false;
			}
          }
        });
		}
			return false;
		});
		
		$("#reindexallpost").click(function(){
			$("#import_record_process").show();
			var appbasesecretkey = $("#appbase_secret_token").val();
			$(this).prop('disabled', true);
		$.ajax({
		  url: ajaxurl,
          type:'POST',
          data:{action:'reindex_all_data', reindexallpost:'1',},
		  beforeSend: function() {
                t = setTimeout("updateprogressstatus()", 2000);
            },
          success: function(responce) {
		  $("#import_record_process").hide();
			jQuery("#postcountprogress").html("0");
			jQuery(".actualprocess").css("width","0%");
			jQuery("#precentageprogress").html(parseInt("0"));
		  $("#reindexallpost").prop('disabled', false);
          var patt = new RegExp("error");
		  var finderror = patt.test(responce);
            if(finderror == true)
			{
				alert("There are some problem occured in index all post");
				window.location.href = adminurl + '?page=methi_search';
				return false;
			}
			else
			{
				alert("Your all posts has been successfully reindex");
				window.location.href = adminurl + '?page=methi_search';
				return false;
			}
          }
        });
		 return false;
		});
	});
})(jQuery);

function updateprogressstatus(){
	jQuery.getJSON(pluginurl+'lib/progresscount.json', function(data){
		var items = [];
		percentComplete = 0;
		//alert(data.toSource());
		if(data){
			var total = data['total'];
			var current = data['currentpost'];   
			var percentComplete = Math.floor((parseInt(current) / parseInt(total)) * 100);
			var finalpercentage = parseInt(percentComplete)+"%";
			//alert(percentComplete);
			if(isNaN(percentComplete)){
				jQuery("#postcountprogress").html("0");
				jQuery(".actualprocess").css("width","0%");
				jQuery("#precentageprogress").html(parseInt("0"));
			}
			else{
				jQuery("#postcountprogress").html(current);
				jQuery(".actualprocess").css("width",finalpercentage);
				jQuery("#precentageprogress").html(parseInt(percentComplete));
			}
		} 
		
		if(percentComplete < 100 || isNaN(percentComplete)){
			t = setTimeout("updateprogressstatus()", 500);
		}
	});
}