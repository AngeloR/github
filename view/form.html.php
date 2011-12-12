<?php $edit_mode = isset($project); ?>
<h3>
	<?php echo ($edit_mode)?'Editing: '.$project->name:'Create a Project'; ?>
</h3>

<form action="<?php echo url_for('___settings','github'); ?>" method="post">
	<?php if($edit_mode): ?>
		<input type="hidden" name="_method" value="put">
		<input type="hidden" name="id" value="<?php echo $project->id; ?>">
	<?php endif; ?>
	
	<label>Project Name</label>
	<div class="input">
		<input type="text" name="name" value="<?php echo ($edit_mode)?$project->name:''; ?>">
		<span class="help-block">This is just so that you know what this GitHub project is called</span>
	</div>
	<br> <br>
	<label>Directory Path</label>
	<div class="input">
		<input type="text" name="directory_path" value="<?php echo ($edit_mode)?$project->path:''; ?>">
		<span class="help-block">Enter a direct path to the directory you want to use to hold your commit logs. The path is the url to the directory where the Worknotes directory will appear</span>
	</div>
	
	<div class="actions">
		<button type="submit" class="btn primary">Save</button>
		<?php if($edit_mode): ?>
			</form>
			<form action="<?php echo url_for('settings','github'); ?>" method="post">
				<input type="hidden" name="_method" value="delete">
				<input type="hidden" name="id" value="<?php echo $project->id; ?>">
				
				<button type="submit" class="btn danger pull-right">Delete</button>
		<?php endif; ?>
	</div>
</form>