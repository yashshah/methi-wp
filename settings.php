<?php global $pluginUrl; ?>
<h2>Methi Search Plugin</h2>
<form name="allpostsend_form" action="" method="post">
	<table id="methiauth"> 
		<tr>
			<td colspan="3">
				<input type="button" name="reindexallpost" value="Re-sync Data" id="reindexallpost" class="indexpostbutton"/>
			</td>
		</tr>
	</table>
</form>
<?php $count_posts = wp_count_posts()->publish; ?>
<div id="import_record_process">
	<h2>Resyncing all posts...</h2>
	<div class="percentagebar">
		<div class="actualprocess">
		</div>
	</div>
	<div class="progress_container">
		<div class="percentage_container">
			<span id="precentageprogress">0</span>%
		</div>
		<div class="totalpostcount">
			<span id="postcountprogress">0</span> /
			<?php echo $count_posts; ?>
			<div>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	#import_record_process {
		background :#DDD;
		padding:5px 15px 20px;
		width:400px;
		margin:20px 0;
		display:none;
	}
	.percentagebar {
		display:inline-block;
		width:100%;
		height:15px;
		background:#ccc;
		position:relative;
		margin:5px 0 20px 0;
	}
	.actualprocess {
		display:inline-block;
		width:0%;
		height:15px;
		background:#019183;
		transition: all 0.5s ease 0s;
		-webkit-transition: all 0.5s ease 0s;
		-o-transition: all 0.5s ease 0s;
		-ms-transition: all 0.5s ease 0s;
		-moz-transition: all 0.5s ease 0s;
	}
	.progress_container {
		display:block;
		width:100%;
		font-size:17px;
	}
	.percentage_container {
		font-size:17px;
		font-weight:bold;
		display:inline;
	}
	.totalpostcount {
		text-align :right;
		float:right;
		display:block;
	}
</style>
