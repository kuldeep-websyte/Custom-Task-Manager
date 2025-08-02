<?php
/*
Plugin Name: Custom Task Manager 
Description: Frontend-only role-based task management system
Version: 1.0
Author: Kuldeep Kumar
*/

if (!defined('ABSPATH')) {
    exit;
}

define('TASK_MANAGER_PRO_VERSION', '1.0');
define('TASK_MANAGER_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TASK_MANAGER_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'task_manager_pro_activate');

function task_manager_pro_activate() {
    require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-task-manager-pro.php';
    require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-roles.php';
    require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-database.php';
    
    Task_Manager_Pro_Roles::create_roles();
    Task_Manager_Pro_Database::create_tables();
    

    $plugin = Task_Manager_Pro::instance();
    $plugin->create_pages();
}


require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-task-manager-pro.php';

function task_manager_pro_init() {
    return Task_Manager_Pro::instance();
}
add_action('plugins_loaded', 'task_manager_pro_init');