<?php global $pluginUrl; ?>
<h2>Imprort All Post</h2>
<form name="allpostsend_form" action="" method="post">
<table id="methiauth"> 
	<tr>
        <td colspan="3"><input type="button" name="submit_allpost" value="Index Post" id="submitallpost"/></td>
    </tr>
</table>
</form>

<div id="loader">
	<img src="<?php echo $pluginUrl; ?>lib/img/loader.gif" alt="loader" title="loader"/>
</div>

<style type="text/css">
#loader{ display:none; }
</style>