<?php $edit_mode = isset($project); ?>
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
		<input type="text">
		<span class="help-block">Enter a direct path to the directory you want to use to hold your commit logs.</span>
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