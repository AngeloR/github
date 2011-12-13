<?php

class Plugin_github_Controller extends Controller {
	
	public static function post_receive_hook($post_receive) {
		$project = R::findOne('github_project','post_receive = ?',array($post_receive));


		
		if(!empty($project)) {
			$payload = $_POST['payload'];
			$payload = json_decode($payload);
			Model_Log::log('info',$payload);
			$text = self::parse_text($payload['repository']);
			
			// if the project path exists, continue
			$path = Plugin_github::project_path_exists($project->path);
			if($path) {
				
				foreach($payload['commits'] as $i => $commit) {
					$ptext = $text.self::parse_commit($commit);
					
					// Write this node!
					$node = R::dispense('node');
					
					$parent = $node->new_node(Model_Node::make_path($path->path,$path->safetitle),$title,$ptext);
					$id = R::store($node);
					
					if($parent->title !== 'Root') {
						$parent->child_id = $id;
						R::store($parent);
					}
				}
			}
		}
		
		d('',true);
		die();
	}
	
	public static function parse_text($repo) {
		$text = "*[".$repo['name']."](".$repo['url'].")*\r\n";
		return $text;
	}
	
	public static function parse_commit($commit) {
		$text = "*id: *[".$commit['id']."](".$commit['url'].")";
		$text .= "\r\n*By:* ".$commit['author'];
		$text .= "\r\n\r\n".$commit['message'];
		
		return $text;
	}
	
	public static function list_projects() {
		set('page_title','GitHub Integration');
		set('breadcrumbs','');
		
		$source = R::getAll('select id,name,post_receive,path from github_project order by name asc');
		$content = '';

		
		if(count($source) >= 1) {
			$dg = new OPCDataGrid($source);
			$dg->fields(array(
				'name' => 'Project Name',
				'path' => 'Directory',
				'post_receive' => 'Post/Receive Hook'
			));
			
			$dg->modify('post_receive', function($val,$row){
				return $_SERVER['SERVER_NAME'].url_for('___settings','github','post-receive',$val);
			});
			
			$content = $dg->build();
		}

		
		$content .= Layout_Helper::partial('plugin/github/view/form.html.php');
		
		
		
		return html($content);
	}
	
	public static function create_integration_handler() {
		
		$name = trim($_POST['name']);
		$path = Model_Node::current_node($_POST['directory_path']);
		$node = array();
		
		$string = array();
		
		// check if the worknotes directory exists in this path
		
		$wn_path = Model_Node::make_path($path->path,$path->safetitle);
		$node['worknotes'] = Model_Node::current_node(Model_Node::make_path($wn_path,'Worknotes'));
		if(empty($node['worknotes'])) {
			$node['worknotes'] = R::dispense('node');
			$node['worknotes']->new_node($wn_path, 'Worknotes');
			$string[] = 'Created worknotes directory at '.$node['worknotes']->path;
		}
		else {
			$string[] = 'Using directory worknotes at '.$node['worknotes']->path;
		}
		
		
		// check if the year directory exists in this path
		$y_path = Model_Node::make_path($wn_path,'Worknotes');
		$this_year = date('Y');
		$node['year'] = Model_Node::current_node(Model_Node::make_path($y_path,$this_year));
		if(empty($node['year'])) {
			$node['year'] = R::dispense('node');
			$node['year']->new_node($y_path,$this_year);
			$string[] = 'Created '.$this_year.' directory at '.$node['year']->path;
		}
		else {
			$string[] = 'Using directory '.$this_year.' at '.$node['year']->path;
		}

		
		// check if the month directory exists in this path
		$m_path = Model_Node::make_path($node['year']->path,$this_year);
		$this_month = date('F');
		$node['month'] = Model_Node::current_node(Model_Node::make_path($m_path,$this_month));
		if(empty($node['month'])) {
			$node['month'] = R::dispense('node');
			$node['month']->new_node($m_path, $this_month);
			$string[] = 'Created '.$this_month.' directory at '.$node['month']->path;
		}
		else {
			$string[] = 'Using directory '.$this_month.' at '.$node['month']->path;
		}
		
		
		

		$github_project = R::dispense('github_project');
		$github_project->name = $name;
		$github_project->path = $node['worknotes']->path;
		$github_project->post_receive = Plugin_github::generate_post_receive($node['year']->path);
		
		$test = R::findOne('github_project','post_receive = ?',array($github_project->post_receive));
		if(empty($test)) {
			R::store($github_project);
			R::store($node['worknotes']);
			R::store($node['year']);
			R::store($node['month']);
			
			$string[]  = 'New GitHub Integration project created.';
			
			alert('success block-message', '<ul><li>'.implode('</li><li>',$string).'</li></ul>');
		}
		else {
			alert('error block-message', 'That project already exists. You need to assign a unique name to a project.');
		}
		
		return self::list_projects();
	}
}