<div class="task-manager-register-form">
 <?php if (isset($_GET['register'])): ?>
        <div class="alert alert-<?php echo $_GET['register'] === 'success' ? 'success' : 'danger'; ?>">
            <?php 
            if ($_GET['register'] === 'success') {
                _e('Registration successful!', 'task-manager-pro');
            } else {
                echo isset($_GET['message']) ? esc_html(urldecode($_GET['message'])) : 
                     __('Registration failed. Please try again.', 'task-manager-pro');
            }
            ?>
        </div>
    <?php endif; ?>
    
    <form method="post" action="">
        <?php wp_nonce_field('task-manager-register', '_wpnonce'); ?>
        
        <div class="mb-3">
            <label for="username" class="form-label"><?php _e('Username', 'task-manager-pro'); ?></label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label"><?php _e('Email', 'task-manager-pro'); ?></label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label"><?php _e('Password', 'task-manager-pro'); ?></label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="role" class="form-label"><?php _e('Role', 'task-manager-pro'); ?></label>
            <select name="role" id="role" class="form-select" required>
                <option value="team_member"><?php _e('Team Member', 'task-manager-pro'); ?></option>
                <option value="project_manager"><?php _e('Project Manager', 'task-manager-pro'); ?></option>
            </select>
        </div>
        
        <button type="submit" name="task_manager_register" class="btn btn-primary">
            <?php _e('Register', 'task-manager-pro'); ?>
        </button>
    </form>
</div>