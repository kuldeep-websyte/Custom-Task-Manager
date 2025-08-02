<?php
class Task_Manager_Pro_Shortcodes {
    public function __construct() {
        add_shortcode('task_login', array($this, 'login_form'));
        add_shortcode('task_logout', array($this, 'logout_button'));
        add_shortcode('task_dashboard', array($this, 'dashboard'));
        add_shortcode('task_user_list', array($this, 'user_list'));
        add_shortcode('task_create_task', array($this, 'create_task_form'));
        add_shortcode('task_all_tasks', array($this, 'all_tasks'));
        add_shortcode('task_my_tasks', array($this, 'my_tasks'));
        add_shortcode('register_member', array($this, 'register_members'));
    }
    
    public function login_form() {
        if (is_user_logged_in()) {
            return '<div class="alert alert-info">' . __('You are already logged in.', 'task-manager-pro') . '</div>';
        }
        
        ob_start();
       
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/login-form.php';
        return ob_get_clean();
    }
    
    public function logout_button() {
        if (!is_user_logged_in()) return '';
        
        ob_start();
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/logout-button.php';
        return ob_get_clean();
    }
    
    public function dashboard() {
        if (!is_user_logged_in()) {
            return '<div class="alert alert-warning">' . __('Please login to access the dashboard.', 'task-manager-pro') . '</div>';
        }
        
        ob_start();
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/dashboard.php';
        return ob_get_clean();
    }
    
    public function user_list() {
        if (!current_user_can('project_manager')) {
            return '<div class="alert alert-danger">' . __('Access denied.', 'task-manager-pro') . '</div>';
        }
        
        ob_start();
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/user-list.php';
        return ob_get_clean();
    }
    
    public function create_task_form() {
        if (!current_user_can('project_manager')) {
            return '<div class="alert alert-danger">' . __('Access denied.', 'task-manager-pro') . '</div>';
        }
        
        ob_start();
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/create-task-form.php';
        return ob_get_clean();
    }
    
    public function all_tasks() {
        if (!current_user_can('project_manager')) {
            return '<div class="alert alert-danger">' . __('Access denied.', 'task-manager-pro') . '</div>';
        }
        
        ob_start();
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/all-tasks.php';
        return ob_get_clean();
    }
    
    public function my_tasks() {
        if (!is_user_logged_in()) {
            return '<div class="alert alert-warning">' . __('Please login to view your tasks.', 'task-manager-pro') . '</div>';
        }
        
        ob_start();
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/my-tasks.php';
        return ob_get_clean();
    }

    public function register_members(){
         if (is_user_logged_in()) {
            return '<div class="alert alert-info">' . __('You are already logged in.', 'task-manager-pro') . '</div>';
        }
        
        ob_start();
        include TASK_MANAGER_PRO_PLUGIN_DIR . 'templates/register-form.php';
        return ob_get_clean();
    }
}