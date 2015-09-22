<?php global $pluginUrl; ?>
<div class="methi-container">
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
</div>
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
		</div>
	</div>
</div>