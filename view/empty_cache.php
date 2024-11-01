<?php if (count(debug_backtrace()) === 0) exit(); ?>

<h2><?php _e('Empty the cache', $domain); ?> - <?php echo self::PLUGIN_NAME; ?></h2>

<p><?php _e('Empty the DB table for cache.', $domain); ?></p>
<form method="post" action="<?php echo admin_url('admin.php?page='. self::PAGE_EMPTY_CACHE); ?>">
	<p>
		<input id="sbm_info_confirm" type="checkbox" name="<?php echo self::PAGE_EMPTY_CACHE; ?>" value="1" />
		<label for="sbm_info_confirm"><?php _e('Please check to confirm', $domain); ?></label>
	</p>
	<p><input class="button" type="submit" value="<?php _e('Empty the cache', $domain); ?>" /></p>
</form>
