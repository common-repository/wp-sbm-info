<?php
/*
Plugin Name: WP SBM Info
Plugin URI: http://rewish.org/wp/sbm_info
Description: ブックマーク数やブックマークコメントなどを記事に貼り付けるためのプラグイン。
Author: Hiroshi Hoaki
Version: 0.1.5
Author URI: http://rewish.org/
*/
if (!class_exists('Services_SBM_Info')) {
	set_include_path(dirname(__FILE__) . '/library' . PATH_SEPARATOR . get_include_path());
	require_once 'Services/SBM/Info.php';
}
require_once dirname(__FILE__) . '/functions.php';

class WP_SBM_Info
{
	const OPTION_VERSION   = 1;
	const OPTION_NAME      = 'wp_sbm_info';
	const DB_TABLE_NAME    = 'sbm_info';
	const PAGE_EMPTY_CACHE = 'sbm_info_empty_cache';
	const PAGE_UNINSTALL   = 'sbm_info_uninstall';
	const PLUGIN_NAME      = 'WP SBM Info';

	private $SBMInfo, $wpdb, $cache, $option, $lastGetUrl;

	private function __construct()
	{
		global $wpdb, $table_prefix;
		$this->wpdb = &$wpdb;
		$this->table = $table_prefix . self::DB_TABLE_NAME;
		$this->createTable();

		$this->getOption();

		$this->SBMInfo = new Services_SBM_Info;
		$this->SBMInfo->setServices($this->option['services'])
		              ->setProxy($this->option['proxy']['host'],
		                         $this->option['proxy']['port']);

		$this->term = time() - ($this->option['term_hour']  * 3600);

		if (!empty($_GET[__CLASS__])) {
			if ($_GET[__CLASS__] !== $this->option['security']) {
				exit(0);
			}
			add_action('wp', array($this, 'bgExecute'));
		} else {
			add_action('wp', array($this, 'init'));
			add_action('admin_menu', array($this, 'adminMenu'));
			add_action('plugins_loaded', array($this, 'loadTextdomain'));
		}
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->SBMInfo, $method), $args);
	}

	public static function getInstance()
	{
		static $obj = null;
		return $obj ? $obj : $obj = new self;
	}

	public function getDomain()
	{
		return __CLASS__;
	}

	public function bgExecute()
	{
		if (!is_single()) return;
		$url = get_permalink();
		$title = get_the_title();
		$this->execute($url, $title, false);
		exit(0);
	}

	private function _readyBgExecute($url)
	{
		$sep = strpos($url, '?') === false ? '?' : '&';
		$url = urlencode($url . $sep . __CLASS__ . '=' . $this->option['security']);
		exec('php '. dirname(__FILE__) .'/bg_exec.php '. $url .' > /dev/null &');
	}

	public function init()
	{
		if (!is_single()) return;
		$url = get_permalink();
		$title = get_the_title();
		if (!$this->isUpdate($url)) return;
		/*
		if ($this->option['bg_exec']) {
			return $this->_readyBgExecute($url);
		}
		*/
		$this->execute($url, $title, false);
	}

	public function execute($url, $title, $cache = true)
	{
		$ret = $this->find($url);
		if ($cache && $ret !== null && $ret->modified > $this->term
				&& $ret->modified > $this->option['updated']) {
			return;
		}
		/*
		if ($this->option['bg_exec']) {
			return $this->_readyBgExecute($url);
		}
		*/
		$this->SBMInfo->setUrl($url)
		              ->setTitle($title);
		$this->save($url, !empty($ret->modified) ? $ret->info : $this->SBMInfo->getAll());
		$this->SBMInfo->execute();
		$this->save($url, $this->SBMInfo->getAll());
	}

	public function isUpdate($url)
	{
		$ret = $this->find($url);
		if ($ret !== null && $ret->modified > $this->term
				&& $ret->modified > $this->option['updated']) {
			return false;
		}
		return true;
	}

	public function getAll($url, $title)
	{
		if ($this->lastGetUrl === $url) {
			return $this->cache;
		}
		$this->lastGetUrl = $url;
		$ret = $this->find($url);
		if ($ret !== null) {
			return $this->cache = unserialize($ret->info);
		}
		$ret = $this->SBMInfo->setUrl($url)
		                     ->setTitle($title)
		                     ->getAll();
		return $this->cache = $ret;
	}

	private function createTable()
	{
		$this->wpdb->query("CREATE TABLE IF NOT EXISTS `$this->table` (
			`url` varchar(255) NOT NULL,
			`info` longtext NOT NULL,
			`modified` int(11) NOT NULL,
			PRIMARY KEY (`url`)
		) ENGINE = MyISAM CHARSET = ". DB_CHARSET .";");
	}

	public function loadTextdomain()
	{
		load_plugin_textdomain($this->getDomain(), false, basename(dirname(__FILE__)) .'/lang');
	}

	public function adminMenu()
	{
		$domain = $this->getDomain();

		$basename = self::PLUGIN_NAME .  __(' Config', $domain);
		add_submenu_page(
			'options-general.php', $basename, $basename,
			8, __FILE__, array($this, 'adminOption')
		);
		if (empty($_GET['page'])) return;

		add_submenu_page(
			'admin.php', __('Empty the cache', $domain) . ' - ' . self::PLUGIN_NAME, null,
			8, self::PAGE_EMPTY_CACHE, array($this, 'adminEmptyCache')
		);
		if ($_GET['page'] === self::PAGE_EMPTY_CACHE) {
			add_action('admin_init', array($this, '_adminEmptyCache'));
		}
		add_submenu_page(
			'admin.php', __('uninstall', $domain) . ' - ' . self::PLUGIN_NAME, null,
			8, self::PAGE_UNINSTALL, array($this, 'adminUninstall')
		);
		if ($_GET['page'] === self::PAGE_UNINSTALL) {
			add_action('admin_init', array($this, '_adminUninstall'));
		}
	}

	public function adminOption()
	{
		$optionName = self::OPTION_NAME;
		$option = $this->getOption();
		$domain = $this->getDomain();
		require dirname(__FILE__) . '/view/option.php';
	}

	public function adminEmptyCache()
	{
		$domain = $this->getDomain();
		require dirname(__FILE__) .'/view/empty_cache.php';
	}

	public function _adminEmptyCache()
	{
		if (empty($_POST[self::PAGE_EMPTY_CACHE]) || !$_POST[self::PAGE_EMPTY_CACHE]) return;
		$this->truncate();
		$file = plugin_basename(__FILE__);
		$pluginHome = admin_url("options-general.php?page=$file&". self::PAGE_EMPTY_CACHE ."=true");
		header("location: $pluginHome");
		exit(0);
	}

	public function adminUninstall()
	{
		$domain = $this->getDomain();
		require dirname(__FILE__) .'/view/uninstall.php';
	}

	public function _adminUninstall()
	{
		if (empty($_POST[self::PAGE_UNINSTALL]) || !$_POST[self::PAGE_UNINSTALL]) return;
		$plugin = plugin_basename(__FILE__);
		deactivate_plugins($plugin);
		update_option('recently_activated',
			array($plugin => time()) + (array)get_option('recently_activated')
		);
		delete_option(self::OPTION_NAME);
		$this->drop();
		wp_redirect('plugins.php?deactivate=true&plugin_status=all&paged=1');
		exit(0);
	}

	private function getOption()
	{
		$this->option = get_option(self::OPTION_NAME);
		if (empty($this->option)) {
			$this->option = array('version' => 0);
		}
		if (self::OPTION_VERSION > $this->option['version']) {
			$this->updateOption();
		}
		return $this->option;
	}

	private function updateOption()
	{
		$this->option = $this->option + array(
			'security'  => sha1(uniqid(rand())),
			'services'  => array('Hatena', 'Delicious'),
			'term_hour' => 12,
			'bg_exec'   => false,
			'proxy'     => array(
				'host' => '',
				'port' => ''
			)
		);
		$this->option['version'] = self::OPTION_VERSION;
		update_option(self::OPTION_NAME, $this->option);
	}

	/**
	 * DB
	 */
	private function find($url)
	{
		$ret = $this->wpdb->get_results("
			SELECT `info`, `modified`
			FROM `$this->table`
			WHERE `url` = '$url'
			LIMIT 1
		");
		return !empty($ret[0]) ? $ret[0] : null;
	}

	private function save($url, $info)
	{
		if (is_string($info)) {
			$info = unserialize($info);
		}
		$cache = $this->find($url);
		// Insert
		if ($cache === null) {
			return $this->insert($url, $info);
		}
		// Update
		$cache = unserialize($cache->info);
		foreach ($info as $name => &$data) {
			if ((int)$data['count'] === 0 && isset($cache[$name])) {
				$data = $cache[$name];
			}
		}
		return $this->update($url, $info);
	}

	private function insert($url, $info)
	{
		if (!is_string($info)) {
			$info = serialize($info);
		}
		return $this->wpdb->query("
			INSERT INTO `$this->table` (`url`, `info`, `modified`)
			VALUES ('$url', '$info', ". time() .");
		");
	}

	private function update($url, $info)
	{
		if (!is_string($info)) {
			$info = serialize($info);
		}
		return $this->wpdb->query("
			UPDATE `$this->table`
			SET `info` = '$info', `modified` = ". time() ."
			WHERE `url` = '$url'
		");
	}

	private function truncate()
	{
		return $this->wpdb->query("TRUNCATE TABLE `$this->table`");
	}

	private function drop()
	{
		return $this->wpdb->query("DROP TABLE `$this->table`");
	}
}
WP_SBM_Info::getInstance();
