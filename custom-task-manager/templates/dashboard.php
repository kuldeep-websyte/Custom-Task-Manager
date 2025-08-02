<div class="task-manager-dashboard">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5><?php _e('Navigation', 'task-manager-pro'); ?></h5>
                </div>
                <div class="card-body">
                    <?php if (current_user_can('project_manager')): ?>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="<?php echo home_url('/task-user-list'); ?>" class="nav-link">
                                    <?php _e('User Management', 'task-manager-pro'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo home_url('/task-create-task'); ?>" class="nav-link">
                                    <?php _e('Create Task', 'task-manager-pro'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo home_url('/task-all-tasks'); ?>" class="nav-link">
                                    <?php _e('All Tasks', 'task-manager-pro'); ?>
                                </a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="<?php echo home_url('/task-my-tasks'); ?>" class="nav-link">
                                    <?php _e('My Tasks', 'task-manager-pro'); ?>
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>
                    
                    <?php echo do_shortcode('[task_logout]'); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <?php if (current_user_can('project_manager')): ?>
                <div class="card">
                    <div class="card-header">
                        <h5><?php _e('Project Manager Dashboard', 'task-manager-pro'); ?></h5>
                    </div>
                    <div class="card-body">
                     
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5><?php _e('Team Member Dashboard', 'task-manager-pro'); ?></h5>
                    </div>
                    <div class="card-body">
                      
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>