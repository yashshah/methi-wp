<?php global $pluginUrl; ?>

<div class="methi-container">
<h2>Methi Search Plugin</h2>
<form name="autentication_form" action="" method="post" class="methi-mainform">
	<table id="methiauth"> 
		<tr>
			<h3>Get your Methi Magic Key</h3>
		</tr>
		<tr>
			<p>
				Thanks for installing Methi Search! Please enter your Methi Magic Key in the field below as found on <a href=
				"http://methi.io/installation.html">methi installation</a> page. If you haven't used Methi before, it will ask you
				to login via Google or Github to generate your unique magic key.
			</p>
			<p>
				Once you enter the key, click 'Sync Data' to complete your installation. From now on, Methi will automatically 
				sync new posts and changes to existing post data.
			</p>
		</tr>
		<tr class="form-row">
			<td>Methi Magic Key: </td>
			<td><input type='text' name='appbase_secret_token' class="appbaseform-elements" id="appbase_secret_token" />
			<input type="button" name="submitallpost" value="Sync Data" id="submitallpost" class="indexpostbutton"/>
			</td>
		</tr>
	</table>
</form>
</div>
<?php $count_posts = wp_count_posts()->publish; ?>
<div id="import_record_process">
	<h2>Syncing all posts...</h2>
	<div class="percentagebar">
		<div class="actualprocess">
		</div>
	</div>
	<div class="progress_container">
		<div class="percentage_container">
			<span id="precentageprogress">0</span>%
		</div>
		<div class="totalpostcount">
			<span id="postcountprogress">0</span>/
			<?php echo $count_posts; ?>
		</div>
	</div>
</div>

