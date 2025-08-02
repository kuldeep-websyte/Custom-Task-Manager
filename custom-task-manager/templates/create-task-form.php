<form method="post" action="">
    <?php wp_nonce_field('task-manager-create-task', '_wpnonce'); ?>
    
    <div class="mb-3">
        <label for="title" class="form-label"><?php _e('Title', 'task-manager-pro'); ?></label>
        <input type="text" name="title" id="title" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label"><?php _e('Description', 'task-manager-pro'); ?></label>
        <textarea name="description" id="description" class="form-control" required></textarea>
    </div>
    
    <div class="mb-3">
        <label for="team_member_id" class="form-label"><?php _e('Assign to Team Member', 'task-manager-pro'); ?></label>
        <select name="team_member_id" id="team_member_id" class="form-select" required>
           
            <option value=""><?php _e('Select Team Member', 'task-manager-pro'); ?></option>
            <?php 
          
            $team_members = get_users(array('role' => 'team_member'));
            foreach ($team_members as $member) {
                echo '<option value="'.$member->ID.'">'.$member->display_name.'</option>';
            }
            ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="due_date" class="form-label"><?php _e('Due Date', 'task-manager-pro'); ?></label>
        <input type="date" name="due_date" id="due_date" class="form-control" required>
    </div>
    
    <button type="submit" name="task_manager_create_task" class="btn btn-primary w-100">
        <?php _e('Create Task', 'task-manager-pro'); ?>
    </button>
</form>