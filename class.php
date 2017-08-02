<?php

class UdimiOptin
{
	const PLUGIN_NAME = 'udimioptin';
	const PLUGIN_FOLDER = 'udimi-optin';
	const OPTIN_CODE_TTL_HOURS = 24;

	public function registerPlugin($plugin_array)
	{
		$plugin_array[self::PLUGIN_NAME] = get_site_url().'/index.php?scplugin';
		return $plugin_array;
	}

	public function addOptinScript()
	{
        if(get_option('udimi_optin_connected')){
            echo get_option('udimi_optin_script', '');
        }
	}

	public function addAdminMenu()
	{
		add_menu_page('Udimi Optin', 'Udimi Optin', 'manage_options', 'udimi-optin-settings', [$this, 'renderSettings'], plugins_url(self::PLUGIN_FOLDER . '/images/logo16.png'));

		add_submenu_page('udimi-optin-settings', 'Settings', 'Settings', 'manage_options', 'udimi-optin-settings', [$this, 'renderSettings']);
	}

	public function renderSettings()
	{
		include(dirname(__FILE__).'/admin/settings.php');
	}

	public static function getScript($key = null)
	{
		// get the key
		if (!$key){
			$key = get_option('udimi_optin_key');
		}
		// check errors
		if (!$key){
			return ['error' => 'Please set up Udimi Api key'];
		}
		if (($content = file_get_contents(self::getApiUrl($key)))===false){
			return ['error' => 'Cannot access Udimi API service'];
		}
		if (!$data = json_decode($content, true)){
			return ['error' => 'Invalid server response'];
		}
		if (!empty($data['error'])){
			return ['error' => $data['error']];
		}
		if (empty($data['script'])){
			return ['error' => 'Invalid data'];
		}
		// return
		return ['error' => '', 'script' => $data['script']];
	}

	private static function getApiUrl($key)
	{
		return "http://".self::getUdimiDomain()."/site/optinScript?hash=" . $key;
	}

	public static function getUdimiDomain(){
		return $_SERVER['REMOTE_ADDR']=='127.0.0.1' ? 'udimi.loc' : 'udimi.com';
	}

    public function getUdimiScheme(){
        return $_SERVER['REMOTE_ADDR']=='127.0.0.1' ? 'http' : 'https';
    }

	public function updateScript()
	{
		$key = get_option('udimi_optin_key');
		if ($key) {
			$date = get_option('udimi_optin_date');
			if (!$date || !strtotime($date) || time() - strtotime($date) > self::OPTIN_CODE_TTL_HOURS * 3600) {
				$data = self::getScript($key);
				if (!empty($data['script'])) {
					update_option('udimi_optin_script', $data['script']);
					update_option('udimi_optin_date', date('Y-m-d H:i:s'));
				}
			}
		}
	}

	public function addAdminScript()
	{
		wp_enqueue_script('jquery');
		wp_register_script('udimi_optin_admin_js', plugins_url(self::PLUGIN_FOLDER.'/js/admin.js'), array('jquery'));
		wp_localize_script(
			'udimi_optin_admin_js',
			'config',
			array(
				'getCodeUrl' => self::getUdimiScheme() . '://' . self::getUdimiDomain() . '/site/optinConnect',
				'updateCodeUrl' => admin_url('admin.php?page=udimi-optin-settings'),
				'toggleStatusUrl' => admin_url('admin.php?page=udimi-optin-settings'),
				'key' => get_option('udimi_optin_key'),
			)
		);
		wp_enqueue_script('udimi_optin_admin_js',false,array('jquery'));
	}

	public static function saveUdimiCode($data){
		update_option('udimi_optin_key',trim($data['hash']));
		if(!empty($data['script'])){
			update_option('udimi_optin_script', stripslashes($data['script']));
			update_option('udimi_optin_date', date('Y-m-d H:i:s'));
		}
        if(!empty($data['name'])){
            update_option('udimi_optin_name', trim($data['name']));
        }
        if(!empty($data['email'])){
            update_option('udimi_optin_email', trim($data['email']));
        }
        if(!empty($data['force_connect'])){
            update_option('udimi_optin_connected', 1);
        }
	}

    public static function toggleStatus()
    {
        $opt = 'udimi_optin_connected';
        $connected = get_option($opt, false);
        if($connected){
            delete_option($opt);
        }else{
            update_option($opt, 1);
        }
    }
}