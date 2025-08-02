<div class="task-manager-my-tasks">
    <h3 class="mb-4"><?php _e('My Tasks', 'task-manager-pro'); ?></h3>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php _e('Title', 'task-manager-pro'); ?></th>
                    <th><?php _e('Description', 'task-manager-pro'); ?></th>
                    <th><?php _e('Status', 'task-manager-pro'); ?></th>
                    <th><?php _e('Due Date', 'task-manager-pro'); ?></th>
                    <th><?php _e('Actions', 'task-manager-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                global $wpdb;
                $table_name = $wpdb->prefix . 'task_manager_tasks';
                $user_id = get_current_user_id();
                $tasks = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM $table_name WHERE team_member_id = %d ORDER BY due_date ASC",
                    $user_id
                ));
                
                foreach ($tasks as $task): ?>
                    <tr data-task-id="<?php echo $task->id; ?>">
                        <td><?php echo esc_html($task->title); ?></td>
                        <td><?php echo esc_html(wp_trim_words($task->description, 10)); ?></td>
                        <td>
                            <select class="form-select task-status" data-task-id="<?php echo $task->id; ?>">
                                <option value="open" <?php selected($task->status, 'open'); ?>><?php _e('Open', 'task-manager-pro'); ?></option>
                                <option value="in-progress" <?php selected($task->status, 'in-progress'); ?>><?php _e('In Progress', 'task-manager-pro'); ?></option>
                                <option value="in-review" <?php selected($task->status, 'in-review'); ?>><?php _e('In Review', 'task-manager-pro'); ?></option>
                                <option value="pending" <?php selected($task->status, 'pending'); ?>><?php _e('Pending', 'task-manager-pro'); ?></option>
                                <option value="completed" <?php selected($task->status, 'completed'); ?>><?php _e('Completed', 'task-manager-pro'); ?></option>
                            </select>
                        </td>
                        <td><?php echo $task->due_date ? date('M j, Y', strtotime($task->due_date)) : __('No due date', 'task-manager-pro'); ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary view-task-details" data-task-id="<?php echo $task->id; ?>">
                                <?php _e('View', 'task-manager-pro'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskDetailsModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="taskDetailsModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>