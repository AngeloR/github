<?php

class Plugin_github_Controller extends Controller {
	
	public static function post_receive_hook() {
		
	}
	
	public static function list_projects() {
		set('page_title','GitHub Integration');
		set('breadcrumbs','');
		
		$source = R::getAll('select id,name,post_receive,path from github_project order by title asc');
		$content = '';

		
		if(count($source) >= 1) {
			$dg = new OPCDataGrid($source);
			$dg->fields(array(
				'name' => 'Project Name',
				'path' => 'Directory',
				'post_receive' => 'Post/Receive Hook'
			));
			
			$content = $dg->build();
		}

		
		$content .= Layout_Helper::partial('plugin/github/view/form.html.php');
		
		
		
		return html($content);
	}
}