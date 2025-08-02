<div class="task-manager-user-list">
    <?php if (isset($_GET['user'])): ?>
        <?php 
        $user_message = '';
        $message_class = 'alert-success';
        
        switch ($_GET['user']) {
            case 'created':
                $user_message = __('User created successfully!', 'task-manager-pro');
                break;
            case 'updated':
                $user_message = __('User updated successfully!', 'task-manager-pro');
                break;
            case 'deleted':
                $user_message = __('User deleted successfully!', 'task-manager-pro');
                break;
            case 'validation_failed':
                $user_message = __('Please fill in all required fields.', 'task-manager-pro');
                $message_class = 'alert-danger';
                break;
            case 'username_exists':
                $user_message = __('Username already exists.', 'task-manager-pro');
                $message_class = 'alert-danger';
                break;
            case 'email_exists':
                $user_message = __('Email already registered.', 'task-manager-pro');
                $message_class = 'alert-danger';
                break;
            case 'creation_failed':
                $user_message = __('Failed to create user. Please try again.', 'task-manager-pro');
                $message_class = 'alert-danger';
                break;
            case 'update_failed':
                $user_message = __('Failed to update user. Please try again.', 'task-manager-pro');
                $message_class = 'alert-danger';
                break;
            case 'delete_failed':
                $user_message = __('Failed to delete user. Please try again.', 'task-manager-pro');
                $message_class = 'alert-danger';
                break;
            case 'cannot_delete_self':
                $user_message = __('You cannot delete your own account.', 'task-manager-pro');
                $message_class = 'alert-warning';
                break;
            case 'has_tasks':
                $user_message = __('Cannot delete user with assigned tasks.', 'task-manager-pro');
                $message_class = 'alert-warning';
                break;
        }
        ?>
        <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $user_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><?php _e('User Management', 'task-manager-pro'); ?></h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus"></i> <?php _e('Add New User', 'task-manager-pro'); ?>
        </button>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title"><?php _e('Project Managers', 'task-manager-pro'); ?></h5>
                    <h3><?php echo count(get_users(['role' => 'project_manager'])); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title"><?php _e('Team Members', 'task-manager-pro'); ?></h5>
                    <h3><?php echo count(get_users(['role' => 'team_member'])); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title"><?php _e('Active Users', 'task-manager-pro'); ?></h5>
                    <h3><?php echo count(get_users(['role__in' => ['project_manager', 'team_member']])); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title"><?php _e('Total Tasks', 'task-manager-pro'); ?></h5>
                    <h3><?php 
                        global $wpdb;
                        echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}task_manager_tasks");
                    ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"><?php _e('All Users', 'task-manager-pro'); ?></h5>
                </div>
                <div class="col-md-6">
                    <select id="role-filter" class="form-select">
                        <option value=""><?php _e('All Roles', 'task-manager-pro'); ?></option>
                        <option value="project_manager"><?php _e('Project Managers', 'task-manager-pro'); ?></option>
                        <option value="team_member"><?php _e('Team Members', 'task-manager-pro'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="userTable">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'task-manager-pro'); ?></th>
                            <th><?php _e('Email', 'task-manager-pro'); ?></th>
                            <th><?php _e('Role', 'task-manager-pro'); ?></th>
                            <th><?php _e('Tasks Assigned', 'task-manager-pro'); ?></th>
                            <th><?php _e('Last Login', 'task-manager-pro'); ?></th>
                            <th><?php _e('Actions', 'task-manager-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $users = get_users(['role__in' => ['project_manager', 'team_member']]);
                        foreach ($users as $user): 
                            $user_roles = $user->roles;
                            $role = in_array('project_manager', $user_roles) ? 'project_manager' : 'team_member';
                           
                            global $wpdb;
                            $task_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}task_manager_tasks WHERE team_member_id = %d",
                                $user->ID
                            ));
                           
                            $last_login = $wpdb->get_var($wpdb->prepare(
                                "SELECT login_time FROM {$wpdb->prefix}task_manager_logins 
                                 WHERE user_id = %d AND login_status = 'success' 
                                 ORDER BY login_time DESC LIMIT 1",
                                $user->ID
                            ));
                        ?>
                            <tr data-role="<?php echo esc_attr($role); ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <?php echo get_avatar($user->ID, 40); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo esc_html($user->display_name); ?></strong>
                                            <br>
                                            <small class="text-muted">@<?php echo esc_html($user->user_login); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo esc_html($user->user_email); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $role === 'project_manager' ? 'primary' : 'success'; ?>">
                                        <?php echo $role === 'project_manager' ? __('Project Manager', 'task-manager-pro') : __('Team Member', 'task-manager-pro'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $task_count; ?></span>
                                </td>
                                <td>
                                    <?php echo $last_login ? date('M j, Y g:i A', strtotime($last_login)) : __('Never', 'task-manager-pro'); ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-user-btn" 
                                                data-user-id="<?php echo $user->ID; ?>"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($user->ID !== get_current_user_id()): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn" 
                                                data-user-id="<?php echo $user->ID; ?>"
                                                data-user-name="<?php echo esc_attr($user->display_name); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php _e('Add New User', 'task-manager-pro'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" method="post">
                <div class="modal-body">
                    <?php wp_nonce_field('task-manager-add-user', '_wpnonce'); ?>
                    
                    <div class="mb-3">
                        <label for="new_username" class="form-label"><?php _e('Username', 'task-manager-pro'); ?></label>
                        <input type="text" class="form-control" id="new_username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_email" class="form-label"><?php _e('Email', 'task-manager-pro'); ?></label>
                        <input type="email" class="form-control" id="new_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label"><?php _e('Password', 'task-manager-pro'); ?></label>
                        <input type="password" class="form-control" id="new_password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_role" class="form-label"><?php _e('Role', 'task-manager-pro'); ?></label>
                        <select class="form-select" id="new_role" name="role" required>
                            <option value=""><?php _e('Select Role', 'task-manager-pro'); ?></option>
                            <option value="project_manager"><?php _e('Project Manager', 'task-manager-pro'); ?></option>
                            <option value="team_member"><?php _e('Team Member', 'task-manager-pro'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel', 'task-manager-pro'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Add User', 'task-manager-pro'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php _e('Edit User', 'task-manager-pro'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="post">
                <div class="modal-body">
                    <?php wp_nonce_field('task-manager-edit-user', '_wpnonce'); ?>
                    <input type="hidden" id="edit_user_id" name="user_id">
                    
                    <div class="mb-3">
                        <label for="edit_display_name" class="form-label"><?php _e('Display Name', 'task-manager-pro'); ?></label>
                        <input type="text" class="form-control" id="edit_display_name" name="display_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label"><?php _e('Email', 'task-manager-pro'); ?></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_role" class="form-label"><?php _e('Role', 'task-manager-pro'); ?></label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="project_manager"><?php _e('Project Manager', 'task-manager-pro'); ?></option>
                            <option value="team_member"><?php _e('Team Member', 'task-manager-pro'); ?></option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label"><?php _e('New Password (leave blank to keep current)', 'task-manager-pro'); ?></label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Cancel', 'task-manager-pro'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Update User', 'task-manager-pro'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
