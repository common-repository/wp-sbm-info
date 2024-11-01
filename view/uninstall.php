<?php if (count(debug_backtrace()) === 0) exit(); ?>

<h2><?php _e('Uninstall', $domain); ?> - <?php echo self::PLUGIN_NAME; ?></h2>

<p><?php _e('Remove the DB table for cache, Remove the option, Disable the plugin.', $domain); ?></p>
<form method="post" action="<?php echo admin_url('admin.php?page='. self::PAGE_UNINSTALL); ?>">
	<p>
		<input id="sbm_info_confirm" type="checkbox" name="<?php echo self::PAGE_UNINSTALL; ?>" value="1" />
		<label for="sbm_info_confirm"><?php _e('Please check to confirm', $domain); ?></label>
	</p>
	<p><input class="button" type="submit" value="<?php _e('Uninstall', $domain); ?>" /></p>
</form>
