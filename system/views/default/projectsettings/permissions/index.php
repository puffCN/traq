<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projectsettings/_nav'); ?>
<form action="<?php echo Request::full_uri(); ?>" method="post">
	<table class="permissions list">
		<thead>
			<tr>
				<th><?php echo l('action'); ?></th>
			<?php foreach ($groups as $group) { ?>
				<th><?php echo $group->name; ?></th>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($actions as $key => $action) { ?>
			<?php if (is_array($action)) { ?>
			<tr class="permission_section">
				<td colspan="<?php echo count($groups) + 1; ?>"><strong><?php echo l($key); ?></strong></td>
			</tr>
			<?php foreach ($action as $act) {
				echo View::render('projectsettings/permissions/_permission_row', array('key' => $key, 'action' => $act));
				}
			} else {
				echo View::render('projectsettings/permissions/_permission_row', array('action' => $action));
			}
		} ?>
		</tbody>
	</table>
	<div class="actions">
		<?php echo Form::submit(l('save')); ?>
	</div>
</form>