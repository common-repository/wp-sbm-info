<?php if (count(debug_backtrace()) === 0) exit(); ?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php echo self::PLUGIN_NAME; ?><?php _e(' Config', $domain); ?></h2>

<?php if (!empty($_GET[self::PAGE_EMPTY_CACHE])): ?>
<div id="message" class="updated fade"><p><strong><?php _e('I empty the cache', $domain); ?></strong></p></div>
<?php endif; ?>

<form method="post" action="options.php">
	<div class="hiddens">
		<?php wp_nonce_field('update-options'); ?>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="<?php echo $optionName; ?>" />
		<input type="hidden" name="<?php echo $optionName; ?>[updated]" value="<?php echo time(); ?>" />
	</div>

	<table class="form-table">
		<tbody>
			<tr>
				<th><?php _e('Enable services', $domain); ?></th>
				<td>
					<ul>
<?php				foreach (array('Hatena'    => __('Hatena bookmark', $domain),
					              'Delicious' => 'Delicious',
					              'Livedoor'  => __('livedoor clip', $domain),
					              'Buzzurl'   => 'Buzzurl',
					              'Twitter'   => 'Twitter') as $key => $name): ?>
						<li>
							<input type="checkbox" id="SBMInfo_<?php echo $key; ?>" name="<?php echo $optionName; ?>[services][]" value="<?php echo $key ?>"<?php if (in_array($key, $option['services'])) echo ' checked="checked"'; ?> />
							<label for="SBMInfo_<?php echo $key; ?>"><?php echo $name; ?></label>
						</li>
<?php				endforeach; ?>
					</ul>
				</td>
				<td></td>
			</tr>
			<tr>
				<th><?php _e('Cache expiration', $domain); ?></th>
				<td><input type="text" style="width: 3em;text-align: center;" name="<?php echo $optionName; ?>[term_hour]" value="<?php echo $option['term_hour']; ?>" /> <?php _e('hour', $domain); ?></td>
				<td><?php _e('Be careful about access restrictions!', $domain); ?></td>
			</tr>
			<tr>
				<th><?php _e('Execute in background', $domain); ?></th>
				<td>
					<input disabled="disabled" type="radio" id="SBMInfo_bg_exec_enable" name="<?php echo $optionName; ?>[bg_exec]" value="1"<?php if ($option['bg_exec']) echo ' checked="checked"'; ?> />
					<label for="SBMInfo_bg_exec_enable" style="margin: 0 10px 0 0;"><?php _e('Enable', $domain); ?></label>
					<input disabled="disabled" type="radio" id="SBMInfo_bg_exec_disable" name="<?php echo $optionName; ?>[bg_exec]" value="0"<?php if (!$option['bg_exec']) echo ' checked="checked"'; ?> />
					<label for="SBMInfo_bg_exec_disable"><?php _e('Disable', $domain); ?></label>
				</td>
				<td><?php _e('Eliminates the delay in API data acquisition.', $domain); ?> (<?php _e('CGI mode only', $domain); ?>)</td>
			</tr>
			<tr>
				<th><?php _e('Proxy', $domain); ?></th>
				<td>
					<label for="SBMInfo_proxy_host"><?php _e('Host:', $domain); ?></label>
					<input id="SBMInfo_proxy_host" type="text" style="width: 8em;margin: 0 10px 0 0;" name="<?php echo $optionName; ?>[proxy][host]" value="<?php echo $option['proxy']['host']; ?>" />
					<label for="SBMInfo_proxy_port"><?php _e('Port:', $domain); ?></label>
					<input id="SBMInfo_proxy_port" type="text" style="width: 3em;" name="<?php echo $optionName; ?>[proxy][port]" value="<?php echo $option['proxy']['port']; ?>" />
				</td>
				<td></td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save', $domain); ?>" />
	</p>

	<h3><?php _e('Other actions', $domain); ?></h3>
	<p><a href="<?php echo admin_url('admin.php?page='. self::PAGE_EMPTY_CACHE); ?>" class="button-primary"><?php _e('Empty the cache', $domain); ?></a></p>
	<p><a href="<?php echo admin_url('admin.php?page='. self::PAGE_UNINSTALL); ?>" class="button-primary"><?php _e('Uninstall', $domain); ?></a></p>

</form>
</div>
