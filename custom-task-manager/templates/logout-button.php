<div class="task-manager-logout">
    <form method="post" action="<?php echo esc_url(wp_logout_url(home_url('/task-login'))); ?>" class="d-inline">
        <button type="submit" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt"></i> <?php _e('Logout', 'task-manager-pro'); ?>
        </button>
    </form>
</div>
