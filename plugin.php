<?php
include('Plugin_github_Controller.php');
class Plugin_github extends Abstract_Plugin implements Plugin_Interface {
	
	public static $github_favicon = 'http://google.com/s2/favicons?domain=github.com';
	
	public function register() {
		d('Plugin_github: register');
		
		$hook = new Captain_Hook();
		$hook->register('github', 'public-routes', array('Plugin_github', 'public_routes'));
		$hook->register('github', 'private-routes', array('Plugin_github', 'private_routes'));
		$hook->register('github', 'settings-menu', array('Plugin_github', 'settings_menu'));
	}
	
	public function unregister() {
		d('Plugin_github: unregister');
	}
	
	public function public_routes() {
		dispatch_post('/___settings/github/post-receive/:post_receive', array('Plugin_github_Controller','post_receive_hook'));
		
	}
	
	public function private_routes() {
		dispatch_get('/___settings/github', array('Plugin_github_Controller', 'list_projects'));
		dispatch_post('/___settings/github', array('Plugin_github_Controller', 'create_integration_handler'));
		dispatch_delete('/___settings/github', array());
		dispatch_put('/___settings/github', array());
	}
	
	public function settings_menu() {
		$menu = array('/___settings/github' => array('display' => '<img src="'.self::$github_favicon.'"> GitHub Integration'));
		
		// Generic way to add items to a menu handler.
		$menu_helper = new Menu_Helper();
		$menu_helper->add($menu,'/___settings');
		$menu_helper->parse();
	}
	
	/**
	 * 
	 * This generates the same key if the same unique key is passed in. For the 
	 * GitHub integration plugin, we use the full path to the worknotes directory
	 * @param unknown_type $unique_key
	 */
	public static function generate_post_receive($unique_key) {
		return sha1($unique_key.md5($unique_key.'38h1v'));
	}
	
	public static function project_path_exists($path_to_worknotes) {
		$pieces = explode('/',$path);
		$pieces[] = 'Worknotes';
		$pieces[] = date('Y');	// current year
		$month = date('F');
		$path = implode('/',$pieces);
		
		return R::dispense('node','path = ? and title = ?',array($path,$month));
		
	}
}
