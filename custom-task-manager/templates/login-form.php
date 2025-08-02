<div class="task-manager-login-form">
    <?php if (isset($_GET['login']) && $_GET['login'] === 'failed'): ?>
       <div class="alert alert-danger"><?php echo __('Login failed. Please try again.', 'task-manager-pro'); ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
      
        
        <div class="mb-3">
            <label for="username" class="form-label"><?php echo __('Username', 'task-manager-pro'); ?></label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label"><?php echo __('Password', 'task-manager-pro'); ?></label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <div class="mb-3 form-check">
            <input type="checkbox" name="rememberme" id="rememberme" class="form-check-input">
            <label for="rememberme" class="form-check-label"><?php echo __('Remember Me', 'task-manager-pro'); ?></label>
        </div>
        
        <button type="submit" name="task_manager_login" class="btn btn-primary">
            <?php echo __('Login', 'task-manager-pro'); ?>
        </button>
    </form>
</div>
