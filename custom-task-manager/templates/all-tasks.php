<div class="task-manager-all-tasks">
    <?php if (isset($_GET['task'])): ?>
        <?php 
        $task_message = '';
        $message_class = 'alert-success';
        
        switch ($_GET['task']) {
            case 'created':
                $task_message = __('Task created successfully!', 'task-manager-pro');
                break;
            case 'failed':
                $task_message = __('Failed to create task. Please try again.', 'task-manager-pro');
                $message_class = 'alert-danger';
                break;
        }
        ?>
        <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $task_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between mb-4">
        <h3><?php _e('All Tasks', 'task-manager-pro'); ?></h3>
        <div>
            <select id="task-filter" class="form-select">
                <option value="all"><?php _e('All Tasks', 'task-manager-pro'); ?></option>
                <option value="open"><?php _e('Open', 'task-manager-pro'); ?></option>
                <option value="in-progress"><?php _e('In Progress', 'task-manager-pro'); ?></option>
                <option value="in-review"><?php _e('In Review', 'task-manager-pro'); ?></option>
                <option value="pending"><?php _e('Pending', 'task-manager-pro'); ?></option>
                <option value="completed"><?php _e('Completed', 'task-manager-pro'); ?></option>
            </select>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php _e('Title', 'task-manager-pro'); ?></th>
                    <th><?php _e('Assigned To', 'task-manager-pro'); ?></th>
                    <th><?php _e('Status', 'task-manager-pro'); ?></th>
                    <th><?php _e('Due Date', 'task-manager-pro'); ?></th>
                    <th><?php _e('Actions', 'task-manager-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                global $wpdb;
                $table_name = $wpdb->prefix . 'task_manager_tasks';
                $tasks = $wpdb->get_results("SELECT * FROM $table_name ORDER BY due_date ASC");
                
                foreach ($tasks as $task): 
                    $team_member = get_user_by('id', $task->team_member_id);
                ?>
                    <tr data-status="<?php echo esc_attr($task->status); ?>" data-task-id="<?php echo $task->id; ?>">
                        <td><?php echo esc_html($task->title); ?></td>
                        <td><?php echo $team_member ? esc_html($team_member->display_name) : __('Unassigned', 'task-manager-pro'); ?></td>
                        <td>
                            <?php 
                            $status_classes = [
                                'open' => 'primary',
                                'in-progress' => 'info',
                                'in-review' => 'warning',
                                'pending' => 'secondary',
                                'completed' => 'success'
                            ];
                            $badge_class = $status_classes[$task->status] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo esc_attr($badge_class); ?>">
                                <?php echo esc_html(ucfirst(str_replace('-', ' ', $task->status))); ?>
                            </span>
                        </td>
                        <td><?php echo $task->due_date ? date('M j, Y', strtotime($task->due_date)) : __('No due date', 'task-manager-pro'); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-primary edit-task-btn" data-task-id="<?php echo $task->id; ?>">
                                    <?php _e('Edit', 'task-manager-pro'); ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-task-btn" data-task-id="<?php echo $task->id; ?>">
                                    <?php _e('Delete', 'task-manager-pro'); ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php _e('Edit Task', 'task-manager-pro'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel', 'task-manager-pro'); ?></button>
                <button type="button" class="btn btn-primary" id="saveTaskBtn"><?php _e('Save Changes', 'task-manager-pro'); ?></button>
            </div>
        </div>
    </div>
</div>