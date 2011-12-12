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
		dispatch_post('/___settings/github/post-receive', array('Plugin_github_Controller','post_receive_hook'));
		
	}
	
	public function private_routes() {
		dispatch_get('/___settings/github', array('Plugin_github_Controller', 'list_projects'));
		dispatch_post('/___settings/github', array());
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
	
	public function get_projects() {
		
	}
}
