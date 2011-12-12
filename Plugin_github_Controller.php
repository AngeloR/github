<?php

class Plugin_github_Controller extends Controller {
	
	public static function post_receive_hook($post_receive) {
		
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
	
	public static function create_integration_handler() {
		var_dump($_POST);
		$name = trim($_POST['name']);
		$path = Model_Node::current_node($_POST['directory_path']);
		$node = array();
		
		$string = array();
		
		// check if the worknotes directory exists in this path
		$node['worknotes'] = Model_Node::current_node($path->path.'/'.$path->safetitle.'/Worknotes');
		if(empty($node['worknotes'])) {
			$node['worknotes'] = R::dispense('node');
			$node['worknotes'] = $node['worknotes']->new_node($path->path.'/'.$path->safetitle, 'Worknotes');
			$string[] = 'Created worknotes directory at '.$node['worknotes']->safetitle;
		}
		

		// check if the year directory exists in this path
		$node['year'] = Model_Node::current_node($node['worknotes']->path.'/'.$node['worknotes']->safetitle.'/'.date('Y'));
		if(empty($node['year'])) {
			$node['year'] = R::dispense('node');
			$node['year'] = $node['year']->new_node($node['worknotes']->path.'/'.$node['worknotes']->safetitle, date('Y'));
			$string[] = 'Created '.date('Y').' directory at '.$node['year']->path;
		}
		
		// check if the month directory exists in this path
		$node['month'] = Model_Node::current($node['year']->path.'/'.$node['year']->safetitle.'/'.date('F'));
		if(empty($node['month'])) {
			$node['month'] = R::dispense('node');
			$node['month'] = $node['year']->new_node($node['year']->path.'/'.$node['year']->safetitle, date('F'));
			$string[] = 'Created '.date('F').' directory at '.$node['month']->path;
		}
		

		$github_project = R::dispense('github_project');
		$github_project->name = $name;
		$github_project->path = $node['year']->path;
		$github_project->post_receive = Plugin_github::generate_post_receive($node['year']->path);
		
		R::store($github_project);
		$string[]  = 'New GitHub Integration project created.';
		alert('success', '<ul><li>'.implode('</li><li>',$string).'</li></ul>');
		
		return self::list_projects();
	}
}