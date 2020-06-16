<div id="add_new_task_popup" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title"><?php _e( 'Add new task', 'ch' ); ?></h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php _e( 'Close', 'ch' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="create-new-task">
					<div class="form-group row">
						<label for="task_title" class="col-sm-2 col-sm-offset-2 col-form-label">
							<?php _e( 'Task title', 'ch' ); ?>
						</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="task_title" name="task_title"
							       placeholder="<?php _e( 'Title', 'ch' ); ?>">
						</div>
						<div class="form-group row">
							<label for="freelancer" class="col-sm-2 col-sm-offset-2 col-form-label">
								<?php _e( 'Freelancer', 'ch' ); ?>
							</label>
							<div class="col-sm-6">
								<select id="freelancer" name="freelancer" class="form-control">
									<option value=""><?php _e( 'Select freelancer', 'ch' ); ?></option>
									<?php foreach( $freelancers as $freelancer ): ?>
									<?php if( $freelancer->countTasks() > 2) continue; ?>
										<option value="<?php echo $freelancer->id(); ?>">
											<?php echo $freelancer->name(); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-2 col-sm-offset-4">
								<button type="submit" class="btn btn-primary">
									<?php _e( 'Add', 'ch' ); ?>
								</button>
							</div>
						</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<?php _e( 'Close', 'ch' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>